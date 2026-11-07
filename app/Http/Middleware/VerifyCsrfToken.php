<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'monify-webhook',
        'rehoboth-webhook',
        'gtsquadco-webhook',
        'rave-webhook',
        'ogadam-webhook',
        'tbch-webhook',
        'palmpay-webhook',
        'smeplug-webhook',
        'wisper-webhook',
        'autosync-webhook',
        'vtuplug-webhook',
        'naijasub-webhook',
        'amakasub-webhook',
        'zoe-data-webhook',
        'integrity-portal-webhook',
        'sim-server-webhook',

    ];
}
