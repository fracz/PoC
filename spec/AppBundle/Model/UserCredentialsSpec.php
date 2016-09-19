<?php

namespace spec\AppBundle\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use AppBundle\Model\UserCredentials;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserCredentialsSpec
 * @package spec\AppBundle\Model
 * @mixin UserCredentials
 */
class UserCredentialsSpec extends ObjectBehavior
{
    const USERNAME = 'username';
    const ENCODED_PASSWORD = 'encoded_password';

    function let()
    {
        $this->beConstructedWith(self::USERNAME);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UserCredentials::class);
    }

    function it_implement_user_interface()
    {
        $this->shouldImplement(UserInterface::class);
    }

    function it_has_username()
    {
        $this->getUsername()->shouldBe(self::USERNAME);
    }

    function it_not_has_encoded_password_afert_create()
    {
        $this->getPassword()->shouldBe(null);
    }

    function it_not_has_salt()
    {
        $this->getSalt()->shouldBe(null);
    }

    function it_has_default_role()
    {
        $this->getRoles()->shouldBe([UserCredentials::DEFAULT_ROLE]);
    }

    function it_can_assign_encoded_password()
    {
        $this->assignEncodedPassword(self::ENCODED_PASSWORD);

        $this->getPassword()->shouldBe(self::ENCODED_PASSWORD);
    }

    function it_not_has_last_login_date_if_never_logged()
    {
        $this->getLastLoginDate()->shouldBe(null);
    }

    function it_not_has_last_login_ip_if_nevenr_logged()
    {
        $this->getLastLoginIp()->shouldBe(null);
    }

    function it_has_last_login_date_after_logged()
    {
        $ip = '152.222.111.22';
        $date = new \DateTimeImmutable();
        $this->loginSuccessfully($date, $ip);

        $this->getLastLoginDate()->shouldBe($date);
    }

    function it_has_last_login_ip_after_logged()
    {
        $ip = '152.222.111.22';
        $date = new \DateTimeImmutable();
        $this->loginSuccessfully($date, $ip);

        $this->getLastLoginIp()->shouldBe($ip);
    }
}
