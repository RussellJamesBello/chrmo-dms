<?php
/*
    The structure of this class and the way it is registered in App\Http\Kernel
    is copied from Illuminate\View\Middleware\ShareErrorsFromSession. The reason
    for this is that I need an instance of App\Custom\FieldChecker to be always available
    in all views. Since how the availability of $errors variable provided by Laravel
    is implemented is what I need, well, why not just copy its logic, right?
*/

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Contracts\View\Factory as ViewFactory;
use App\Custom\FieldChecker;

class RegisterFieldChecker
{
    protected $view;

    public function __construct(ViewFactory $view)
    {
        $this->view = $view;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*
            I can't die dump this variable's contents even though there are
            errors from the validation. But when I die dump in the view, it works!
            WANT KIND OF SORCERY IS THIS???!!!
        */
        $error_bag = $request->session()->get('errors') ?: new ViewErrorBag;

        $this->view->share('checker', new FieldChecker($error_bag));

        return $next($request);
    }
}
