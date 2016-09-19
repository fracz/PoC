<?php

namespace spec\AppBundle\Security;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bridge\Doctrine\Tests\Fixtures\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Guard\GuardAuthenticatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\HeaderBag;
use AppBundle\Security\Authenticator;
use AppBundle\Token\TokenGenerator;
use AppBundle\Utils\Codes;
use AppBundle\Exception\TokenGeneratorException;
use AppBundle\Model\UserCredentials;

/**
 * Class AuthenticatorSpec
 * @package spec\AppBundle\Security
 * @mixin Authenticator
 */
class AuthenticatorSpec extends ObjectBehavior
{
    const SECRET_KEY = 'secret_key';
    const USERNAME = 'username';

    function let(TokenGenerator $tokenGenerator)
    {
        $this->beConstructedWith($tokenGenerator, self::SECRET_KEY);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Authenticator::class);
    }

    function it_is_guard_authenticator()
    {
        $this->shouldImplement(GuardAuthenticatorInterface::class);
    }

    function it_return_unauthorized_response_when_not_authenticated(Request $request)
    {
        $this->start($request)->getStatusCode()->shouldBe(Codes::HTTP_UNAUTHORIZED);
    }

    function it_skip_authentication_process_when_not_send_authorization_header(
        Request $request,
        HeaderBag $headers
    ) {
        $headers->get('Authorization')->willReturn(null);
        $request->headers = $headers;

        $this->getCredentials($request)->shouldBe(null);
    }

    function it_throw_exception_when_authorization_header_is_not_bearer(
        Request $request,
        HeaderBag $headers
    ) {
        $headers->get('Authorization')->willReturn('Not_Bearer token');
        $request->headers = $headers;

        $this->shouldThrow(AuthenticationException::class)->during('getCredentials', [$request]);
    }

    function it_return_token_from_get_credentials_when_authorization_bearer_header_exists(
        Request $request,
        HeaderBag $headers
    ) {
        $token = 'Bearer token_jwt';
        $headers->get('Authorization')->willReturn($token);
        $request->headers = $headers;

        $this->getCredentials($request)->shouldBe('token_jwt');
    }

    function it_throw_exception_when_get_user_with_bearer_invalid_token(
        UserProviderInterface $userProvider,
        TokenGenerator $tokenGenerator
    ) {
        $jwt = 'invalid_jwt';
        $tokenGenerator->decode($jwt, self::SECRET_KEY)->willThrow(TokenGeneratorException::class);

        $this->shouldThrow(AuthenticationException::class)->during('getUser', [$jwt, $userProvider]);
    }

    function it_throw_exception_if_provider_not_found_user_credentials(
        UserProviderInterface $userProvider,
        TokenGenerator $tokenGenerator
    ) {
        $jwt = 'valid_jwt';
        $payload = [
            'username' => self::USERNAME
        ];
        $tokenGenerator->decode($jwt, self::SECRET_KEY)->willReturn($payload);
        $userProvider->loadUserByUsername(self::USERNAME)->willThrow(UsernameNotFoundException::class);

        $this->shouldThrow(UsernameNotFoundException::class)->during('getUser', [$jwt, $userProvider]);
    }

    function it_return_user_credentials_if_provider_found(
        UserProviderInterface $userProvider,
        TokenGenerator $tokenGenerator,
        UserCredentials $userCredentials
    ) {
        $jwt = 'valid_jwt';
        $payload = [
            'username' => self::USERNAME
        ];
        $tokenGenerator->decode($jwt, self::SECRET_KEY)->willReturn($payload);
        $userProvider->loadUserByUsername(self::USERNAME)->willReturn($userCredentials);

        $this->getUser($jwt, $userProvider)->shouldBeAnInstanceOf(UserCredentials::class);
    }
}
