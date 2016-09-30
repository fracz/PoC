<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 15.09.16
 * Time: 13:39
 */

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Form\Type\LoginType;
use AppBundle\Form\Login;
use AppBundle\Utils\Codes;
use AppBundle\Token\TokenGenerator;
use AppBundle\Event\UserLoggedEvent;

/**
 * Class AuthController
 * @package AppBundle\Controller
 * @Route(service="poc.controller.auth")
 */
class AuthController
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    private $secretKey;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * AuthController constructor.
     * @param FormFactoryInterface $formFactory
     * @param EventDispatcherInterface $eventDispatcher
     * @param TokenGenerator $tokenGenerator
     * @param $secretKey
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EventDispatcherInterface $eventDispatcher,
        TokenGenerator $tokenGenerator,
        $secretKey
    ) {
        $this->formFactory = $formFactory;
        $this->tokenGenerator = $tokenGenerator;
        $this->secretKey = $secretKey;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Request $request
     * @Route("/token", name="auth_token", methods={"POST"})
     * @return array
     */
    public function tokenAction(Request $request)
    {
        $form = $this->formFactory->create(LoginType::class);

        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var Login $data */
            $data = $form->getData();

            $this->dispatchUserLoggedEvent($request, $data);

            return [
                'data' => ['token' => $this->createToken($data)]
            ];
        }

        return [
            'status' => Codes::HTTP_UNAUTHORIZED
        ];
    }

    /**
     * @param Request $request
     * @param Login $login
     */
    private function dispatchUserLoggedEvent(Request $request, Login $login)
    {
        $event = new UserLoggedEvent($login->username, $request->getClientIp(), new \DateTimeImmutable());
        $this->eventDispatcher->dispatch(UserLoggedEvent::NAME, $event);
    }

    /**
     * @param Login $login
     * @return string
     */
    private function createToken(Login $login)
    {
        $payload = [
            'iss' => 'http://example.com',
            'aud' => 'http://example.org',
            'iat' => time(),
            'exp' => time() + 3600,
            'username' => $login->username
        ];

        return $this->tokenGenerator->encode($payload, $this->secretKey);
    }
}