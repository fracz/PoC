<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 16.09.16
 * Time: 11:21
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use AppBundle\Repository\UserCredentialsRepository;

class UserCredentialsValidator extends ConstraintValidator
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var UserCredentialsRepository
     */
    private $userCredentialsRepository;

    /**
     * UserCredentialsValidator constructor.
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param UserCredentialsRepository $userCredentialsRepository
     */
    public function __construct(
        UserPasswordEncoderInterface $userPasswordEncoder,
        UserCredentialsRepository $userCredentialsRepository
    ) {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->userCredentialsRepository = $userCredentialsRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        $userCredentials = $this->userCredentialsRepository->findByUsername($value->username);

        if (!isset($userCredentials)) {
            $this->context->addViolation('invalid credentials');
            return;
        }

        if (!$this->userPasswordEncoder->isPasswordValid($userCredentials, $value->password)) {
            $this->context->addViolation('invalid credentials');
        }
    }
}