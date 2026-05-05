<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\MobileAppToken;

class CheckToken
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
		if($token = $request->token)
		{
			$mobileAppToken = MobileAppToken::getByToken($token);
			if($mobileAppToken)
			{
				if($mobileAppToken->isValid())
				{
					if($mobileAppToken->employee) {
						$mobileAppToken->setLastActiveAt();
						$mobileAppToken->setValidUntil();

						return $next($request);
					} else {
						$mobileAppToken->delete();
					}
				}
			}
		}

		return response()->json([
			'message'	=> 'Require valid token'
		], 401);
	}
}
