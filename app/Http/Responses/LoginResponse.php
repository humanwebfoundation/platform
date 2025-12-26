<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request): Response
    {
        $intendedUrl = redirect()->intended()->getTargetUrl();

        // If redirecting to OAuth authorize, use full page navigation
        // This prevents CORS issues when OAuth auto-approves and redirects externally
        if ($this->isOAuthAuthorizeUrl($intendedUrl)) {
            return Inertia::location($intendedUrl);
        }

        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->intended(route('dashboard'));
    }

    /**
     * Check if the URL is an OAuth authorization URL.
     */
    protected function isOAuthAuthorizeUrl(string $url): bool
    {
        return str_contains($url, '/oauth/authorize');
    }
}

