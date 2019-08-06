<?php

namespace App\Http\Middleware;

use Closure;

class AccessApi
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
        $userKey = 1234;
        $passKey = 1234;
        

        if($request->header('userkey')!=$userKey or $request->header('passkey')!=$passKey)
        {
            return response()->json(['message'=>'Unauthorized'],401);
            
        }

        return $next($request);    
    }
}
