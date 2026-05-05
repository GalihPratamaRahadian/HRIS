<?php

namespace App\Http\Middleware;

use Closure;

class CheckMobileAppApiKey
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
		if(!empty($request->api_key)) {

			if($request->api_key == 'API_KEY') {
				return $next($request);
			}

			return \Res::invalid([
				'message'	=> 'API Key tidak valid'
			]);
		}

		return \Res::invalid([
			'message'	=> 'API Key diperlukan'
		]);
	}
}
