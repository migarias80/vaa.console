<?php

/*
 * Readme:
 * https://github.com/firebase/php-jwt
 * http://stackoverflow.com/questions/29121112/how-to-implement-token-based-authentication-securely-for-accessing-the-website
 */

namespace providers;

use \Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use \Firebase\JWT\BeforeValidException;
use \Firebase\JWT\ExpiredException;
use \exception\InvalidTokenException;
use Datetime;
use DateTimeZone;
use \Exception;

class JWTProvider
{

    function __construct() {

    }

    function Get($uid, $additionalData) {
        $now = new DateTime('now', new DateTimeZone(TIME_ZONE));
        $now = $now->getTimestamp();
        $expire = new DateTime('now', new DateTimeZone(TIME_ZONE));
        $expire = $expire->getTimestamp();
        $expire += 10800;

        $payload = array(
            // "iss" => TOKEN_ACCOUNT,
            // "sub" => TOKEN_ACCOUNT,
            // "aud" => "https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit",
            "iat" => $now,
            "exp" => $expire,  // Maximum expiration time is one hour
            "uid" => $uid
        );
        $payload = array_merge ($payload, $additionalData);
        return JWT::encode($payload, TOKEN_KEY, TOKEN_HASH);
    }

    function Decode($token) {
        try {
            return JWT::decode($token, TOKEN_KEY, array(TOKEN_HASH));
        } catch (SignatureInvalidException $e) {
            throw new InvalidTokenException($e->getMessage());
        } catch (BeforeValidException $e) {
            throw new InvalidTokenException($e->getMessage());
        } catch (ExpiredException $e) {
            throw new InvalidTokenException($e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }
    }

}