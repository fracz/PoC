<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 18.09.16
 * Time: 08:11
 */

namespace Tests\AppBundle\Controller;

use Tests\Context\UserCredentialsContext;
use AppBundle\Utils\Codes;

class AuthControllerTest extends BaseController
{
    /**
     * @var UserCredentialsContext
     */
    protected $userCredentialsContext;

    public function setUp()
    {
        parent::setUp();

        $this->userCredentialsContext = new UserCredentialsContext(
            $this->container->get('doctrine.orm.entity_manager'),
            $this->container->get('user_credentials.factory'),
            $this->container->get('user_credentials.repository')
        );
    }

    public function test_fail_login_by_not_existing_account()
    {
        $this->loginToSystem();

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_UNAUTHORIZED);
    }

    public function test_successfully_login()
    {
        $this->userCredentialsContext->createUserCredentialsInSystem(
            UserCredentialsContext::USERNAME,
            UserCredentialsContext::PASSWORD
        );

        $this->loginToSystem();

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK);

        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('token', $content);
    }

    private function loginToSystem()
    {
        $this->client->request(
            'POST',
            '/api/token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['username' => UserCredentialsContext::USERNAME, 'password' => UserCredentialsContext::PASSWORD])
        );
    }
}