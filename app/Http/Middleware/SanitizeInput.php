<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class SanitizeInput
{
    public function handle(Request $request, Closure $next)
    {
        $invalidPattern = '/(\{\{|\}\}|\{!!|\!!\}|\<|\>|\[|\]|\*|%)/';
        foreach ($request->all() as $key => $value) {
            if (is_string($value) && preg_match($invalidPattern, $value)) {
                return redirect()->back()
                    ->withErrors([
                        $key => 'Invalid characters detected in input.'
                    ])
                    ->withInput();
            }
        }
        return $next($request);
    }
}