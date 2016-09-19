<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 16.09.16
 * Time: 15:28
 */

namespace AppBundle\Exception;

class TokenGeneratorException extends \Exception
{
    public static function payloadNotHasMustClaim($claim)
    {
        return new static(sprintf('Payload must has "%s"', $claim));
    }

    public static function invalidDataToJsonEncode()
    {
        return new static('Invalid data to json encode');
    }

    public static function invalidDataToJsonDecode()
    {
        return new static('Invalid data to json decode');
    }

    public static function keyNotExists()
    {
        return new static('Key must exists');
    }

    public static function invalidNumberSegments()
    {
        return new static('Invalid number segments on decoded jwt');
    }

    public static function invalidDecode($error)
    {
        return new static($error);
    }
}