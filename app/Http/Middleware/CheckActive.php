<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user && $user->active == false && $user->active != true) {
            if($request->ajax()){
                return response()->json(['message' => 'Thao tác không hợp lệ, vui lòng thử lại!'], 403);
            }
            abort(403, 'Thao tác không hợp lệ, vui lòng thử lại!');
        }
        return $next($request);
    }
}
