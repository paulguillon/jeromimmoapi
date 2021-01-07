<?php
namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
     public function handle($request, closure $next)
     {
         $headers = [
             'access-control-allow-origin'      => '*',
             'access-control-allow-methods'     => 'post, get, options, put, delete',
             'access-control-allow-credentials' => 'true',
             'access-control-max-age'           => '86400',
             'access-control-allow-headers'     => 'content-type, authorization, x-requested-with'
         ];

         if ($request->ismethod('options'))
         {
             return response()->json('{"method":"options"}', 200, $headers);
         }

         $response = $next($request);
         foreach($headers as $key => $value)
         {
             $response->header($key, $value);
         }

         return $response;
     }


}
