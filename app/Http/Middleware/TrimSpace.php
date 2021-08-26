<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrimSpace
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $inputs = $request->all();

        // 前後の半角・全角スペースを削除
        $trimmed['label'] = preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $inputs['data']['label']);

        $request->merge($trimmed);

        return $next($request);
    }
}
