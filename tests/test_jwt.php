<?php

declare(strict_types=1);

use Firebase\JWT\JWT;
use GuzzleHttp\Psr7\ServerRequest;
use Luracast\Restler\Auth\JsonWebToken;
use Luracast\Restler\Auth\JsonWebTokenAccessControl;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\ResponseHeaders;
use Luracast\Restler\UserIdentifier;

require __DIR__ . '/../vendor/autoload.php';

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function keyPair(): array
{
    $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    check($key !== false, 'RSA key generation failed');
    check(openssl_pkey_export($key, $private), 'RSA export failed');
    $details = openssl_pkey_get_details($key);
    check(is_array($details), 'Public key export failed');
    return [$private, $details['key']];
}

function authenticate(JsonWebToken $auth, ?string $jwt): array
{
    $request = new ServerRequest('GET', '/', $jwt === null ? [] : ['Authorization' => "Bearer $jwt"]);
    $user = new UserIdentifier($request);
    return [$auth->_isAllowed($request, $user, new ResponseHeaders()), $user];
}

function denied(JsonWebToken $auth, string $jwt, string $label, int $status = 403): void
{
    try {
        authenticate($auth, $jwt);
        throw new RuntimeException("$label unexpectedly accepted");
    } catch (HttpException $e) {
        check($e->getCode() === $status, "$label returned wrong status");
    }
    check($auth->token === null, "$label exposed a rejected token");
    echo "PASS $label\n";
}

[$private, $public] = keyPair();
[$otherPrivate] = keyPair();
$now = time();
JWT::$timestamp = $now;
$claims = ['sub' => '123', 'iss' => 'issuer', 'aud' => 'audience', 'azp' => 'party', 'client_id' => 'client', 'iat' => $now, 'nbf' => $now, 'exp' => $now + 300];
$valid = JWT::encode($claims, $private, 'RS256');
JsonWebToken::$publicKey = $public;
JsonWebToken::$matchedIssuer = 'issuer';
JsonWebToken::$matchedAudience = 'audience';
JsonWebToken::$matchedAuthorizedParty = 'party';
JsonWebToken::$matchedClientIdentifier = 'client';
$auth = new JsonWebToken();

[$allowed, $user] = authenticate($auth, $valid);
check($allowed && $user->getUniqueIdentifier() === '123' && $auth->token->sub === '123', 'Valid RS256 authentication failed');
echo "PASS valid RS256 and subject identity\n";

JsonWebToken::$publicKey = preg_replace('/-----[^-]+-----|\s/', '', $public);
check(authenticate($auth, $valid)[0], 'Bare public key failed');
JsonWebToken::$publicKey = $public;
echo "PASS bare public key\n";

check(authenticate($auth, null)[0] === false && $auth->token === null, 'Missing header did not clear token');
echo "PASS missing header after success\n";

foreach (['iss', 'aud', 'azp', 'client_id'] as $claim) {
    authenticate($auth, $valid);
    denied($auth, JWT::encode(array_replace($claims, [$claim => 'wrong']), $private, 'RS256'), "invalid $claim");
}
$missingSubject = $claims;
unset($missingSubject['sub']);
denied($auth, JWT::encode($missingSubject, $private, 'RS256'), 'missing subject');
denied($auth, JWT::encode(array_replace($claims, ['exp' => $now - 1]), $private, 'RS256'), 'expired token');
denied($auth, JWT::encode(array_replace($claims, ['nbf' => $now + 60]), $private, 'RS256'), 'future nbf');
$future = array_replace($claims, ['iat' => $now + 60]);
unset($future['nbf']);
denied($auth, JWT::encode($future, $private, 'RS256'), 'future iat');
denied($auth, 'not.a.jwt', 'malformed token');
authenticate($auth, $valid);
denied($auth, JWT::encode($claims, $otherPrivate, 'RS256'), 'wrong RSA signature after success');
denied($auth, JWT::encode($claims, $public, 'HS256'), 'HS256 algorithm confusion');
JsonWebToken::$publicKey = '';
denied($auth, $valid, 'missing public key', 500);
JsonWebToken::$publicKey = $public;

$access = new JsonWebTokenAccessControl();
check(authenticate($access, $valid)[0], 'Access-control subclass authentication failed');
$access->roleRequired = true;
try {
    authenticate($access, $valid);
    throw new RuntimeException('Missing required role accepted');
} catch (HttpException $e) {
    check($e->getCode() === 403, 'Missing role returned wrong status');
}
echo "PASS access-control subclass success and role denial\n";
JWT::$timestamp = null;
