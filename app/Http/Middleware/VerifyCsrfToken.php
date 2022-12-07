<?php

namespace App\Http\Middleware;

use Log;
use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    public function handle($request, Closure $next)
    {
        if (
            $this->isReading($request) ||
            $this->runningUnitTests() ||
            $this->inExceptArray($request) ||
            $this->tokensMatch($request)
        ) {
            return $this->addCookieToResponse($request, $next($request));
        }

        // redirect the user back to the last page and show error
        if (!in_array($request->path(), ['login', 'logout'])) {
            $userInfo = $request->user() ? json_encode(['id' => $request->user()->id, 'username' => $request->user()->name]) : '';
            Log::error("TokenMismatchException \nURL: ".$request->fullUrl()." \nIP: ".$request->ip()." \nUser: ".$userInfo." \nToken: ".json_encode($request->session()->token())." \nForm: ".json_encode($request->except(['password']))."\n");
        }
        flash('Invalid form token', 'danger');
        return back();
    }
}
