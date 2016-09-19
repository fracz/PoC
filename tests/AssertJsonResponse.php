<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 18.09.16
 * Time: 08:41
 */

namespace Tests;

use Symfony\Component\HttpFoundation\Response;

trait AssertJsonResponse
{
    protected static function assertJsonResponse(
        Response $response,
        $statusCode = 200,
        $checkValidJson =  true,
        $contentType = 'application/json'
    )
    {
        self::assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        self::assertTrue(
            $response->headers->contains('Content-Type', $contentType),
            $response->headers
        );
        if ($checkValidJson) {
            $decode = json_decode($response->getContent());
            self::assertTrue(($decode != null && $decode != false),
                'is response valid json: [' . $response->getContent() . ']'
            );
        }
    }
}