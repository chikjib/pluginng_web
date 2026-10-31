<?php
// Run with: php tests/regression/palmpay.php
// Isolated doubles keep these flow tests independent of a database or live PalmPay credentials.
namespace Illuminate\Http {
    class Request { public $bank = 'palmpay'; }
}
namespace App\Models {
    class User {
        public static $current;
        public $id = 1;
        public $firstname = 'Test';
        public $lastname = 'User';
        public $email = 'test@example.com';
        public $bvn;
        public $nin;
        public $palmpay_reserved_acct;
        public $palmpay_account;
        public $saves = 0;
        public static function find($id) { return self::$current; }
        public function save() { $this->saves++; }
    }
}
namespace App\Http\Resources {
    class UserResource { public function __construct($user) {} }
}
namespace App\Http\Controllers\API {
    class BaseController {
        public $user;
        public function sendResponse($data, $message) { return ['success' => true, 'code' => 200]; }
        public function sendError($error, $details = [], $code = 404) {
            return ['success' => false, 'code' => $code];
        }
    }
}
namespace Illuminate\Support\Facades {
    class Http {
        public static $payload;
        public static $response;
        public static $fail = false;
        public static function acceptJson() { return new self; }
        public function withHeaders($headers) { return $this; }
        public function withToken($token) { return $this; }
        public function post($url, $payload) {
            self::$payload = $payload;
            if (self::$fail) { throw new \RuntimeException('Provider unavailable'); }
            return $this;
        }
        public function json() { return self::$response; }
    }
}
namespace {
    function config($key) { return 'test-only'; }
    $root = dirname(__DIR__, 2);
    require "$root/app/Traits/WalletTrait.php";
    require "$root/app/Traits/ReferenceTrait.php";
    require "$root/app/Traits/IntegrationsTrait.php";
    require "$root/app/Http/Controllers/API/UserController.php";

    class TestPalmpayController extends \App\Http\Controllers\API\UserController {
        public function generate_signature(array $data, string $private_key) { return 'test-signature'; }
    }
    function check($condition, $message) {
        if (!$condition) { throw new \RuntimeException($message); }
    }
    function identity($value) {
        return $value === null ? null : openssl_encrypt($value, 'AES-128-CTR', 'SWIFtlInk1nG', 0, 'SWiftLiNknG89101');
    }
    function successResponse() {
        return ['respCode' => '00000000', 'respMsg' => 'success', 'status' => true,
            'data' => ['virtualAccountNo' => '0123456789', 'virtualAccountName' => 'Test User']];
    }
    function runCase($bvn, $nin) {
        $user = new \App\Models\User;
        $user->bvn = identity($bvn);
        $user->nin = identity($nin);
        \App\Models\User::$current = $user;
        \Illuminate\Support\Facades\Http::$payload = null;
        $controller = new TestPalmpayController;
        $controller->user = $user;
        $result = $controller->generate_reserved_account(new \Illuminate\Http\Request);
        return [$result, $user, $controller];
    }
    use Illuminate\Support\Facades\Http;
    Http::$response = successResponse();
    foreach ([['01234567890', null, 'personal'], [null, '10987654321', 'personal_nin'],
        ['01234567890', '10987654321', 'personal']] as [$bvn, $nin, $type]) {
        [$result, $user, $controller] = runCase($bvn, $nin);
        check($result['success'], "$type creation should succeed");
        check(Http::$payload['identityType'] === $type, 'Incorrect identity type');
        check(Http::$payload['licenseNumber'] === ($bvn ?? $nin), 'Incorrect identity number');
        check($user->palmpay_account === '0123456789' && $user->saves === 1, 'Account must be persisted');
        check(json_decode($user->palmpay_reserved_acct, true)['accountNumber'] === '0123456789', 'Reserved account must match');
        Http::$payload = null;
        $controller->generate_reserved_account(new \Illuminate\Http\Request);
        check(Http::$payload === null && $user->saves === 1, 'Existing account must not be recreated');
    }
    foreach ([[null, null], ['123', null], [null, 'abcdefghijk']] as [$bvn, $nin]) {
        [$result, $user] = runCase($bvn, $nin);
        check($result['code'] === 422 && Http::$payload === null && $user->saves === 0, 'Invalid identity must be rejected before provider request');
    }
    foreach ([null, [], ['respCode' => 'ERROR', 'status' => false],
        ['respCode' => '00000000', 'status' => true, 'data' => []]] as $response) {
        Http::$response = $response;
        [$result, $user] = runCase(null, '10987654321');
        check($result['code'] === 502 && $user->saves === 0, 'Failed or malformed response must not save an account');
    }
    Http::$fail = true;
    [$result, $user] = runCase('01234567890', null);
    check($result['code'] === 502 && $user->saves === 0, 'Provider exception must return a recoverable error');
    echo "PalmPay backend regression checks passed.\n";
}
