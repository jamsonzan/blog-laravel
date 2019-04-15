<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class Auth
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
        $uid = $request->post('uid');
        $taken = $request->cookie('taken');
        if($uid
            && $taken
            && (
                $valid = User::where('taken', '=', $taken)
                ->where('id', '=', $uid)->count()
            )
        ){
            return $next($request);
        }
        return response()->json(array(
            'code' => 0,
            'message' => 'please login'
        ));
    }
}
