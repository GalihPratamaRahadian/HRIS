<?php

namespace App\Http\Middleware;

use Closure;

class AllowedRole
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next, ...$allowedRoles)
	{
		if(!auth()->guest())
		{
			$role = auth()->user()->role;
			if(in_array($role, $allowedRoles))
			{
				return $next($request);
			}
			else
			{
				return redirect()->back();
			}

		}
		else
		{
			return redirect('login?redirect='.$request->url());
		}
	}
}
