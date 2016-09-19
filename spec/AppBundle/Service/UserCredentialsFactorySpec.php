<?php

namespace spec\AppBundle\Service;

use AppBundle\Model\UserCredentials;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use AppBundle\Service\UserCredentialsFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserCredentialsFactorySpec
 * @package spec\AppBundle\Service
 * @mixin UserCredentialsFactory
 */
class UserCredentialsFactorySpec extends ObjectBehavior
{
    const USERNAME = 'username';
    const PLAIN_PASSWORD = 'plain_password';
    const ENCODED_PASSWORD = 'encoded_password';

    function let(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->beConstructedWith($userPasswordEncoder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UserCredentialsFactory::class);
    }

    function it_throw_exception_when_not_pass_username()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('create', [null, self::PLAIN_PASSWORD]);
    }

    function it_throw_exception_when_username_is_empty()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('create', ["", self::PLAIN_PASSWORD]);
    }

    function it_throw_exception_when_not_pass_password()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('create', [self::USERNAME, null]);
    }

    function it_throw_exception_when_password_is_empty()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('create', [self::USERNAME, ""]);
    }

    function it_return_user_credentials_with_encoded_password(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $userPasswordEncoder->encodePassword(Argument::type(UserCredentials::class), self::PLAIN_PASSWORD)
            ->willReturn(self::ENCODED_PASSWORD);

        $userCredentials = $this->create(self::USERNAME, self::PLAIN_PASSWORD);
        
        $userCredentials->shouldBeAnInstanceOf(UserCredentials::class);
        $userCredentials->getPassword()->shouldBe(self::ENCODED_PASSWORD);
    }
}
