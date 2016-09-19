<?php

namespace spec\AppBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bridge\Doctrine\Tests\Fixtures\User;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraint;
use AppBundle\Repository\UserCredentialsRepository;
use AppBundle\Validator\Constraints\UserCredentialsValidator;
use AppBundle\Form\Login;
use AppBundle\Model\UserCredentials;

/**
 * Class UserCredentialsValidatorSpec
 * @package spec\AppBundle\Validator\Constraints
 * @mixin UserCredentialsValidator
 */
class UserCredentialsValidatorSpec extends ObjectBehavior
{
    const USERNAME = 'username';
    const PLAIN_PASSWORD = 'plain_password';

    function let(
        UserPasswordEncoderInterface $userPasswordEncoder,
        UserCredentialsRepository $userCredentialsRepository,
        ExecutionContextInterface $executionContext,
        UserCredentials $userCredentials
    ) {
        $userCredentialsRepository->findByUsername(self::USERNAME)->willReturn($userCredentials);
        $this->beConstructedWith($userPasswordEncoder, $userCredentialsRepository);
        $this->initialize($executionContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UserCredentialsValidator::class);
    }

    function it_is_constraint_validator()
    {
        $this->shouldImplement(ConstraintValidator::class);
    }

    function it_add_violation_if_not_found_user_credentials(
        UserCredentialsRepository $userCredentialsRepository,
        ExecutionContextInterface $executionContext,
        Constraint $constraint
    ) {
        $userCredentialsRepository->findByUsername(self::USERNAME)->willReturn(null);
        $executionContext->addViolation(Argument::type('string'))->shouldBeCalled();

        $this->validate($this->createLogin(), $constraint);
    }

    function it_add_violation_if_password_not_valid(
        UserPasswordEncoderInterface $userPasswordEncoder,
        ExecutionContextInterface $executionContext,
        Constraint $constraint
    ) {
        $userPasswordEncoder->isPasswordValid(Argument::type(UserCredentials::class), self::PLAIN_PASSWORD)
            ->willReturn(false);
        $executionContext->addViolation(Argument::type('string'))->shouldBeCalled();

        $this->validate($this->createLogin(), $constraint);
    }

    private function createLogin()
    {
        $login = new Login();
        $login->username = self::USERNAME;
        $login->password = self::PLAIN_PASSWORD;

        return $login;
    }
}
