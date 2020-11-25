<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class SuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $all_folders = Storage::allDirectories('scan');

        foreach($all_folders as $office)
        {
            if(Storage::exists($office) && Storage::allFiles($office) == [])
                Storage::deleteDirectory($office);
        }
    }
}
