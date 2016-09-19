<?php

namespace spec\AppBundle\Security;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use AppBundle\Security\UserCredentialsProvider;
use AppBundle\Model\UserCredentials;
use AppBundle\Repository\UserCredentialsRepository;

/**
 * Class UserCredentialsProviderSpec
 * @package spec\AppBundle\Security
 * @mixin UserCredentialsProvider
 */
class UserCredentialsProviderSpec extends ObjectBehavior
{
    const USERNAME = 'username';

    function let(UserCredentialsRepository $userCredentialsRepository)
    {
        $this->beConstructedWith($userCredentialsRepository);
    }
    function it_is_initializable()
    {
        $this->shouldHaveType(UserCredentialsProvider::class);
    }

    function it_is_user_provider()
    {
        $this->shouldImplement(UserProviderInterface::class);
    }

    function it_check_is_support_class()
    {
        $this->supportsClass(UserCredentials::class)->shouldBe(true);
    }

    function it_check_is_not_support_class()
    {
        $this->supportsClass(UserProviderInterface::class)->shouldBe(false);
    }

    function it_throw_exception_when_not_found_user(UserCredentialsRepository $userCredentialsRepository)
    {
        $userCredentialsRepository->findByUsername(self::USERNAME)->willReturn(null);

        $this->shouldThrow(UsernameNotFoundException::class)->during('loadUserByUsername', [self::USERNAME]);
    }

    function it_return_user_credentials_if_found(
        UserCredentialsRepository $userCredentialsRepository,
        UserCredentials $userCredentials
    ) {
        $userCredentialsRepository->findByUsername(self::USERNAME)->willReturn($userCredentials);

        $this->loadUserByUsername(self::USERNAME)->shouldBeAnInstanceOf(UserCredentials::class);
    }
}
