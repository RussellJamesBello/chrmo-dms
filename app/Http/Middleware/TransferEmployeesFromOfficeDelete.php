<?php

namespace App\Http\Middleware;

use App\Custom\OfficeDivisionQuerier;
use App\Employee;

use Closure;

class TransferEmployeesFromOfficeDelete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $office = resolve(OfficeDivisionQuerier::class)->getFromOfficeOrDivision('name_acronym', $request->route('office'));

        $has_division_employees = null;

        if($office instanceof App\Office)
            $has_division_employees = Employee::whereIn('division_id', $office->divisions->pluck('division_id')->toArray())->get()->isNotEmpty();

        if($office == null || $office->employees->isEmpty() || $has_division_employees)
            return back();

        /*
        if($request->is('offices/*'))
        {
            if($request->route('office')->employees->isEmpty())
                return back();
        }

        elseif($request->is('divisions/*'))
        {
            if($request->route('division')->employees->isEmpty())
                return back();
        }
        */

        return $next($request);
    }
}
