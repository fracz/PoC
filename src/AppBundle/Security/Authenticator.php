<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 16.09.16
 * Time: 19:12
 */

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use AppBundle\Utils\Codes;
use AppBundle\Token\TokenGenerator;
use AppBundle\Exception\TokenGeneratorException;

class Authenticator extends AbstractGuardAuthenticator
{
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var
     */
    private $secretKey;

    /**
     * Authenticator constructor.
     * @param TokenGenerator $tokenGenerator
     * @param $secretKey
     */
    public function __construct(TokenGenerator $tokenGenerator, $secretKey)
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->secretKey = $secretKey;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse(null, Codes::HTTP_UNAUTHORIZED);
    }

    public function getCredentials(Request $request)
    {

        if (!$token = $request->headers->get('Authorization')) {
            return null;
        }

        $jwt = explode(' ', $token);

        if (count($jwt) !== 2 || $jwt[0] !== 'Bearer') {
            throw new AuthenticationException('Invalid API key format');
        }

        return $jwt[1];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $payload = $this->tokenGenerator->decode($credentials, $this->secretKey);
            return $userProvider->loadUserByUsername($payload['username']);
        } catch (TokenGeneratorException $tokenGeneratorException) {
            throw new AuthenticationException($tokenGeneratorException->getMessage());
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(null, Codes::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function supportsRememberMe()
    {
        return false;
    }
}