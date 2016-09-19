<?php

namespace spec\AppBundle\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\AuthController;
use AppBundle\Token\TokenGenerator;
use AppBundle\Utils\Codes;
use AppBundle\Form\Login;
use AppBundle\Form\Type\LoginType;
use AppBundle\Event\UserLoggedEvent;

/**
 * Class AuthControllerSpec
 * @package spec\AppBundle\Controller
 * @mixin AuthController
 */
class AuthControllerSpec extends ObjectBehavior
{
    const SECRET_KEY = 'secret_key';
    const USERNAME = 'username';
    const PASSWORD = 'password';
    const TOKEN = 'token';

    function let(
        FormFactoryInterface $formFactory,
        FormInterface $form,
        EventDispatcherInterface $eventDispatcher,
        TokenGenerator $tokenGenerator
    ) {
        $formFactory->create(LoginType::class)->willReturn($form);
        $this->beConstructedWith($formFactory, $eventDispatcher, $tokenGenerator, self::SECRET_KEY);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AuthController::class);
    }

    function it_not_authorize_when_credentials_invalid(
        Request $request,
        FormInterface $form
    ) {
        $form->handleRequest(Argument::type(Request::class))->shouldBeCalled();
        $form->isValid()->willReturn(false);

        $response = $this->tokenAction($request);
        $response->shouldBeAnInstanceOf(JsonResponse::class);
        $response->getStatusCode()->shouldBe(Codes::HTTP_UNAUTHORIZED);
    }

    function it_return_authorized_token_when_credentials_valid(
        Request $request,
        FormInterface $form,
        TokenGenerator $tokenGenerator
    ) {
        $this->successFullyLoginFormRequest($form);
        $tokenGenerator->encode(Argument::allOf(
            Argument::type('array'),
            Argument::withKey('iss'),
            Argument::withKey('aud'),
            Argument::withKey('iat'),
            Argument::withKey('exp'),
            Argument::withEntry('username', self::USERNAME)
        ), self::SECRET_KEY)->willReturn(self::TOKEN);

        $response = $this->tokenAction($request);
        $response->shouldBeAnInstanceOf(JsonResponse::class);
        $response->getStatusCode()->shouldBe(Codes::HTTP_OK);
        $response->getContent()->shouldContain(self::TOKEN);
    }

    function it_logged_last_successfully_login(
        Request $request,
        FormInterface $form,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->successFullyLoginFormRequest($form);
        $eventDispatcher->dispatch(UserLoggedEvent::NAME, Argument::type(UserLoggedEvent::class));

        $this->tokenAction($request);
    }

    private function successFullyLoginFormRequest(FormInterface $form)
    {
        $form->handleRequest(Argument::type(Request::class))->shouldBeCalled();
        $form->isValid()->willReturn(true);
        $login = new Login();
        $login->username = self::USERNAME;
        $login->password = self::PASSWORD;

        $form->getData()->willReturn($login);
    }
}
