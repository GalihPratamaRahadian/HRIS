<?php

namespace App\Http\Middleware;

use Closure;

class CheckPermission
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next, $menu, $accessRequired, $allowForEmployee = 'no')
	{
		if(auth()->user()->isEmployee()) {
			if($allowForEmployee == 'yes') {
				return $next($request);
			}
		}

		if(\UserPermission::check($menu, $accessRequired)) {
			return $next($request);
		}

		abort(403);
	}
}
