<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 16.09.16
 * Time: 17:30
 */

namespace AppBundle\Token;

use Firebase\JWT\JWT;
use AppBundle\Exception\TokenGeneratorException;

class FirebaseTokenGeneratorAdapter implements TokenGenerator
{
    /**
     * @param array $payload
     * @param $key
     * @return string
     */
    public function encode(array $payload, $key)
    {
        return JWT::encode($payload, $key);
    }

    /**
     * @param $jwt
     * @param $key
     * @throws TokenGeneratorException
     * @return array $payload
     */
    public function decode($jwt, $key)
    {
        try {
            return (array) JWT::decode($jwt, $key, ['HS256']);
        } catch (\UnexpectedValueException $exception) {
            throw TokenGeneratorException::invalidDecode($exception->getMessage());
        }
    }
}