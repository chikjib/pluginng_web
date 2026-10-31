<?php

namespace App\Support;

use Laravel\Passport\Passport;
use League\OAuth2\Server\CryptKey;

final class PassportKeyPair
{
    public static function matches(): bool
    {
        $load = static function (string $type): string {
            $value = str_replace('\\n', "\n", config("passport.{$type}_key") ?? '');
            return (new CryptKey($value ?: 'file://'.Passport::keyPath("oauth-{$type}.key"), null, false))->getKeyContents();
        };
        $private = openssl_pkey_get_private($load('private'));
        $public = openssl_pkey_get_public($load('public'));
        if ($private === false || $public === false) return false;
        $derived = openssl_pkey_get_details($private);
        $configured = openssl_pkey_get_details($public);
        return is_array($derived) && is_array($configured) && hash_equals($derived['key'], $configured['key']);
    }
}
