<?php

namespace App\Http\Middleware;

use Closure;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
     public function handle($request, Closure $next, $guard = 'admin')
    {
        // dd(1);
        // config(['auth.defaults.guard' => 'admin']);
        // config(['auth.defaults.passwords' => 'users']);

        if (!\Auth::guard($guard)->check()) {
            //echo $request->user()->id;
            // dd(\Auth::guard($guard));
            // if (substr($request->path(), 0, 16) == "admin/log-viewer" || $request->path() == "admin/elfinder/?CKEditor=content&CKEditorFuncNum=1&langCode=vi") {
            //     return $next($request);
            // }

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            \Session::put('loginRedirect_' . $guard, \Request::url());
            return redirect()->guest('/login');
        }

        // if (substr($request->path(), 0, 16) == "admin/log-viewer" || $request->path() == "admin/elfinder") {
        //     return $next($request);
        // }

        $action = $request->route()->getAction();
        // dd($action);
        if (!isset($action['as']) || \Auth::guard($guard)->user()->hasRole('Superadministrator')) return $next($request);
        // dd(isset($action['role']));
        if (isset($action['role'])) {
            $data = explode('.', $action['role']);
            if (!isset($data[2])) return $next($request);
            if (\Auth::guard($guard)->user()->can(str_replace('-', '_', $data[1]) . '.' . $data[2]))
                return $next($request);
        }

        $data = explode('.', $action['as']);
        if (count($data) <> 2) return $next($request);
        if ($data[1] == 'index' || $data[1] == 'show')
            $action = 'read';
        elseif ($data[1] == 'edit' || $data[1] == 'update')
            $action = 'update';
        elseif ($data[1] == 'create' || $data[1] == 'store')
            $action = 'create';
        elseif($data[1] == 'destroy')
            $action = 'delete';
        else
            $action = $data[1];
        // dd(str_replace('-', '_', $data[0]) . '.' . $action);
        if (!\Auth::guard($guard)->user()->can(str_replace('-', '_', $data[0]) . '.' . $action))
            return redirect()->back();
        return $next($request);
    }
}
