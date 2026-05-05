<?php

namespace App\Http\Middleware;

use Closure;

class HrisApiCheck
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
		$requiredApiKey = setting('hris_api_key', 'API_KEY');
		$receivedApiKey = trim($request->header('key'));

		if($receivedApiKey) {
			if($requiredApiKey == $receivedApiKey) {
				return $next($request);
			} else {
				abort(401, 'Api key tidak valid. Silahkan hubungi web administrator');
			}
		}
		
		abort(401, 'Perlu api key. Silahkan hubungi web administrator');
	}
}
