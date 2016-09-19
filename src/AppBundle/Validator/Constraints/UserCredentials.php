<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 16.09.16
 * Time: 11:18
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UserCredentials extends Constraint
{
    public $message;

    public function validatedBy()
    {
        return 'user_credentials_validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}