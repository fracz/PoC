<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 16.09.16
 * Time: 15:21
 */

namespace AppBundle\Token;

use AppBundle\Exception\TokenGeneratorException;

interface TokenGenerator
{
    /**
     * @param array $payload
     * @param $key
     * @return string
     */
    public function encode(array $payload, $key);

    /**
     * @param $jwt
     * @param $key
     * @throws TokenGeneratorException
     * @return array $payload
     */
    public function decode($jwt, $key);
}