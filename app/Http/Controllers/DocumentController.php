<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use App\Custom\OfficeDivisionQuerier;
use App\Custom\SuffixesTrait;
use Validator;
use App\Office;
use App\Division;
use App\Employee;
use App\Document;
use App\DocumentContent;
use App\DocumentLog;
use Carbon\Carbon;

class DocumentController extends Controller
{
    use SuffixesTrait;
	protected $request;

    public function __construct(Request $request)
    {
    	$this->request = $request;
    }

    public function showAddDocument()
    {
    	return view('document.add', [
                        'title' => 'Add A Document',
                        'offices_and_divisions_json' => resolve(OfficeDivisionQuerier::class)->getAllOfficeAndDivision(),
                        'suffixes_json' => collect($this->name_suffixes)->toJson(),
                    ]);
    }

    public function addDocument()
    {
        if($this->request->ajax())
        {
            /*
                Windows has a limited file path length of 260 characters so I limited the maximum character of folder directory and document name. 
                I reserved the 159 characters for the storage path (45 -> C:/xampp/htdocs/chrmo-dms/storage/app/scan///. The three trailing slahes
                are for office/division and employee folder), office/division folder (max of 10), and employee name folder (max of 104). The 90 is
                shared by custom folders and document name. 8 characters are used by file name.

                LIMIT -                         260
                storage path -                   45
                office/division folder (max) -   10
                employee name folder (max) -    104
                custom folder (min) -            45
                document folder (min) -          45
                file name (max) -                 8
                                                  3 (Difference. 3 characters vacant)
            */
            $cust_folder_dir_and_doc_name_max = 45 + $this->getExtraDirectoryLength();
            $office = resolve(OfficeDivisionQuerier::class)->getFromOfficeOrDivision('name_acronym', $this->request->office);
            $employee = null;

            $validator = Validator::make($this->request->all(), [
                'new_employee' => 'sometimes|in:true',
                'emp_id' => 'bail|required_without:new_employee|exists:employees,employee_id',
                'office' => 'bail|required',
                'first_name' => 'bail|required|alpha_spaces|max:40',
                'middle_name' => 'nullable|bail|alpha_spaces|max:30',
                'last_name' => 'bail|required|alpha_spaces|max:30',
                'suffix' => 'nullable|in:' . implode(',', collect($this->name_suffixes)->flatten()->toArray()),
                //https://stackoverflow.com/questions/11382919/relative-path-regular-expression (added - Ññ so that it will accept these characters)
                'folder_directory' => "nullable|bail|max:$cust_folder_dir_and_doc_name_max|regex:/^(?!-)[a-z0-9- Ññ']+(?<!-)(\/(?!-)[a-z0-9- Ññ']+(?<!-))*$/i",
                'document_name' => "bail|required|alpha_num_spaces|max:$cust_folder_dir_and_doc_name_max",
                'tags' => 'bail|required|max:100',
                'uploads' => 'required|max:800',
                'uploads.*' => 'bail|max:1500|mimes:jpeg,jpg,png',
            ], [
                'uploads.max' => 'You may only upload 800 pages at a time.',
                'uploads.*.max' => 'This image may not be greater than 1.5 Megabytes.',
                'uploads.*.mimes' => 'This image must be a file of type: :values.',
                'uploads.*.uploaded' => 'This image failed to upload.'
            ]);

            $validator->validate();

            $validator->after(function($validator) use($office, &$employee){
                if($office == null)
                    $validator->errors()->add('office', 'The selected office is invalid.');

                if($this->request->new_employee == null && $this->request->emp_id != null)
                {
                    $employee = Employee::with('documents')->find($this->request->emp_id);

                    //tl;dr if database results didn't match what the user submitted
                    if($employee->getOffice()->name_acronym != $this->request->office || $employee->first_name != $this->request->first_name || $employee->middle_name != $this->request->middle_name || $employee->last_name != $this->request->last_name || $employee->suffix_name != $this->request->suffix)
                        $validator->errors()->add('emp_id', "The employee's information you supplied does not match the ones in the records. Please try again.");

                    //verify if the document name already exists
                    foreach($employee->documents as $document)
                        if(strtolower($document->custom_folder_path . '/' . $document->name) == strtolower($this->request->folder_directory . '/' . $this->request->document_name))
                        {
                            $validator->errors()->add('document_name', "The document name already exists.");
                            break;
                        }
                }
            });

            $validator->validate();

            if($employee == null)
            {
                $employee = new Employee;
            
                resolve(OfficeDivisionQuerier::class)->insertEmployeeOffice($employee, $office);

                $employee->first_name = $this->request->first_name;
                $employee->middle_name = $this->request->middle_name;
                $employee->last_name = $this->request->last_name;
                $employee->suffix_name = $this->request->suffix;
                $employee->save();
            }

            $document = new Document;
            $document->employee_id = $employee->employee_id;
            $document->name = $this->request->document_name;
            $document->custom_folder_path = $this->request->folder_directory;
            $document->keywords = $this->request->tags;
            $document->save();

            $path = getEmployeeFolder($employee, $this->request->folder_directory, $this->request->document_name);
            $contents_values = [];
            $timestamp = Carbon::now();

            foreach($this->request->uploads as $key => $page)
            {
                $page_number = $key + 1;
                $file_name = $page_number . '.' . $page->extension();

                Storage::putFileAs($path, $page, $file_name);

                $contents_values[$key] = [
                    'document_id' => $document->document_id,
                    'page_number' => $page_number,
                    'file_name' => $file_name,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ];
            }

            DocumentContent::insert($contents_values);

            $document_log = new DocumentLog;
            $document_log->document_id = $document->document_id;
            $document_log->user_id = $this->request->user()->user_id;
            $document_log->action = 'Added ' . $document->name . ' document of ' . $employee->getWholeName();
            $document_log->save();

            return response()->json();
        }

        return response()->json([], 403);
    }

    public function getDocument(Document $document)
    {
        $pages = [];
        $document = Document::with(['document_contents'])->find($document->document_id);

        foreach($document->document_contents as $content)
            $pages[] = [
                'id' => (string)$content->document_content_id,
                'page_number' => $content->page_number
            ];

        return view('document.show', [
            'title' => "Document Info",
            'document' => $document,
            'pages' => collect($pages),
            'page_url' => substr_replace(route('page', ['document' => $document->document_id, 0]), '', -1),
            'employee' => Employee::find($document->employee->employee_id)
        ]);
    }

    public function editDocument(Document $document)
    {
        if($this->request->isMethod('get'))
        {
            return view('document.edit', [
                'title' => 'Edit Document',
                'document' => $document,
                //'document_info' => $document->with(['document_contents'])->get(),
                'document_contents' => $document->document_contents->pluck('document_content_id'),
                'left_label' => getEmployeeFolder($document->employee, null, null, null, false),
                'page_url' => substr_replace(route('page', ['document' => $document->document_id, 'document_content' => 0]), '', -1)//for front-end's part of loading page images
            ]);
        }

        elseif($this->request->isMethod('put') && $this->request->ajax())
        {
            $cust_folder_dir_and_doc_name_max = 45 + $this->getExtraDirectoryLength();

            $validator = Validator::make($this->request->all(), [
                //https://stackoverflow.com/questions/11382919/relative-path-regular-expression (added - Ññ so that it will accept these characters)
                'uploads_altered' => 'required|in:1,0',
                'folder_directory' => "nullable|bail|max:$cust_folder_dir_and_doc_name_max|regex:/^(?!-)[a-z0-9- Ññ']+(?<!-)(\/(?!-)[a-z0-9- Ññ']+(?<!-))*$/i",
                'document_name' => "bail|required|alpha_num_spaces|max:$cust_folder_dir_and_doc_name_max",
                'tags' => 'bail|required|max:100',
                'uploads' => 'required|max:800',
                'uploads.*' => 'bail|max:1500|mimes:jpeg,jpg,png',
            ], [
                'uploads.max' => 'You may only upload 800 pages at a time.',
                'uploads.*.max' => 'This image may not be greater than 1.5 Megabytes.',
                'uploads.*.mimes' => 'This image must be a file of type: :values.',
                'uploads.*.uploaded' => 'This image failed to upload.'
            ]);

            $validator->after(function($validator) use(&$document){
                $document_path = strtolower($document->custom_folder_path . '/' . $document->name);
                $new_document_path = strtolower($this->request->folder_directory . '/' . $this->request->document_name);

                //verify if the document name already exists
                if($document_path != $new_document_path)
                {
                    $employee = $document->employee;

                    foreach($employee->documents as $current_document)
                        if(strtolower($current_document->custom_folder_path . '/' . $current_document->name) == $new_document_path)
                        {
                            $validator->errors()->add('document_name', "The document name already exists.");
                            break;
                        }
                }
            });

            $validator->validate();

            $old_path = getEmployeeFolder($document->employee, $document->custom_folder_path, $document->name);
            $new_path = getEmployeeFolder($document->employee, $this->request->folder_directory, $this->request->document_name);

            $old_document_name = $document->name;
            $old_folder_directory = $document->custom_folder_path;

            $document->name = $this->request->document_name;
            $document->custom_folder_path = $this->request->folder_directory;
            $document->keywords = $this->request->tags;
            $document->save();

            //contents unchanged and document name or folder directory changed. DONE
            if($this->request->uploads_altered == 0 && ($old_document_name != $this->request->document_name || $old_folder_directory != $this->request->folder_directory))
                Storage::move($old_path, $new_path);

            //contents changed and document name and folder directory unchanged. DONE
            elseif($this->request->uploads_altered == 1 && ($old_document_name == $this->request->document_name && $old_folder_directory == $this->request->folder_directory))
            {
                $contents_values = [];
                $timestamp = Carbon::now();

                //remove all contents of the folder
                $filesystem = new Filesystem;
                $filesystem->cleanDirectory(getEmployeeFolder($document->employee, $this->request->folder_directory, $this->request->document_name, null, true, true));

                foreach($this->request->uploads as $key => $page)
                {
                    $page_number = $key + 1;
                    $file_name = $page_number . '.' . $page->extension();

                    Storage::putFileAs($new_path, $page, $file_name);

                    $contents_values[$key] = [
                        'document_id' => $document->document_id,
                        'page_number' => $page_number,
                        'file_name' => $file_name,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ];
                }

                DocumentContent::where('document_id', $document->document_id)->delete();
                DocumentContent::insert($contents_values);
            }

            //contents changed and document name or folder directory changed. DONE
            elseif($this->request->uploads_altered == 1 && ($old_document_name != $this->request->document_name || $old_folder_directory != $this->request->folder_directory))
            {
                Storage::deleteDirectory($old_path);

                $contents_values = [];
                $timestamp = Carbon::now();

                foreach($this->request->uploads as $key => $page)
                {
                    $page_number = $key + 1;
                    $file_name = $page_number . '.' . $page->extension();

                    Storage::putFileAs($new_path, $page, $file_name);

                    $contents_values[$key] = [
                        'document_id' => $document->document_id,
                        'page_number' => $page_number,
                        'file_name' => $file_name,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ];
                }

                DocumentContent::where('document_id', $document->document_id)->delete();
                DocumentContent::insert($contents_values);
            }

            $document_log = new DocumentLog;
            $document_log->document_id = $document->document_id;
            $document_log->user_id = $this->request->user()->user_id;
            $document_log->action = 'Edited ' . $document->name . ' document of ' . $document->employee->getWholeName();
            $document_log->save();

            return response()->json();
        }

        return response()->json([], 403);
    }

    public function getPage(Document $document, DocumentContent $document_content)
    {
        if($document->document_id == $document_content->document_id)
        {
            $document = $document_content->document;
            $employee = $document->employee;

            $path = getEmployeeFolder($employee, $document->custom_folder_path, $document->name, $document_content->file_name, true, true);

            return response()->file($path, ['Cache-Control' => 'no-cache, no-store']);
        }

        return response()->json([], 404);
    }

    public function getTags()
    {
        if($this->request->tag != null)
        {
            $length = strlen($this->request->tag);

            //the array's index may have skipped numbers due to the filter function, reindex it by array_values() so the front-end can understand it
            return ['results' => array_values(Document::select('keywords')
                                ->search($this->request->tag, ['keywords' => 1])
                                ->take(50)
                                ->get()
                                ->transform(function($item, $key){//convert all keywords as array
                                    return explode(',', $item->keywords);
                                })
                                ->flatten()//make all results as one array
                                ->unique()//remove all duplicates
                                ->filter(function($item, $key) use($length){//get only of what's identical from the request
                                    if(strtolower(substr($item, 0, $length)) === strtolower($this->request->tag))
                                        return $item;
                                })
                                ->transform(function($item, $key){//format the result so the front-end can understand it
                                    return ['name' => $item, 'value' => $item];
                                })->toArray())
            ];
        }

        return [];
    }

    public function removeDocument(Document $document)
    {
        $id = $document->employee->employee_id;

        $deleted = Storage::deleteDirectory(getEmployeeFolder($document->employee, $document->custom_folder_path, $document->name));

        if($deleted)
            $document->delete();

        return redirect()->route('employee', ['employee' => $id]);
    }

    private function getExtraDirectoryLength()
    {
        /*
            The literal length (45 characters) of the variable using this function's return value assumes that the name fields and office 
            field have met their maximum character length (acronym is used for the office). If ever these fields do not meet
            their maximum character length, the difference of their maximum character length and the character length of their
            value will be added to the variable above.
        */
        $office = resolve(OfficeDivisionQuerier::class)->getFromOfficeOrDivision('name_acronym', $this->request->office);

        $extra_length_first_name = $this->getPartialDirectoryLength(40, studly_case($this->request->first_name));
        $extra_length_middle_name = $this->getPartialDirectoryLength(30, studly_case($this->request->middle_name));
        $extra_length_last_name = $this->getPartialDirectoryLength(30, studly_case($this->request->last_name));
        $extra_length_suffix = $this->getPartialDirectoryLength(4, $this->request->suffix);
        $extra_length_office = $this->getPartialDirectoryLength(10, $office ? $office->name_acronym : null);

        $extra_length = ($extra_length_first_name + $extra_length_middle_name + $extra_length_last_name + $extra_length_suffix + $extra_length_office) / 2;

        //if $extra_length is a double because it was an odd number when it was added, just disregard the decimal and return it as int
        return (int)$extra_length;
    }

    private function getPartialDirectoryLength($limit, $str)
    {
        $length = strlen($str);

        if($limit >= $length)
            return $limit - $length;

        return 0;
    }
}