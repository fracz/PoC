<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 18.09.16
 * Time: 09:30
 */

namespace Tests\AppBundle\Controller;

use AppBundle\Model\UserCredentials;
use Tests\Context\UserCredentialsContext;
use Tests\Context\CatContext;
use AppBundle\Utils\Codes;
use AppBundle\Model\Cat;

class CatsControllerTest extends BaseController
{
    const CAT_URL = 'http://cat.pl';

    /**
     * @var UserCredentialsContext
     */
    protected $userCredentialsContext;

    /**
     * @var CatContext
     */
    protected $catContext;

    public function setUp()
    {
        parent::setUp();

        $this->userCredentialsContext = new UserCredentialsContext(
            $this->container->get('doctrine.orm.entity_manager'),
            $this->container->get('user_credentials.factory'),
            $this->container->get('user_credentials.repository')
        );

        $this->catContext = new CatContext(
            $this->container->get('doctrine.orm.entity_manager'),
            $this->container->get('cat.repository')
        );
    }

    public function test_empty_list_cats_in_system()
    {
        $this->client->request('GET', '/api/cats');

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK, false);

        $catsList = json_decode($this->client->getResponse()->getContent(), true);
        self::assertCount(0, $catsList);
    }

    public function test_not_empty_list_cats_in_system()
    {
        $this->catContext->addCats([
            new Cat('http://cat1.pl', 'creator', new \DateTimeImmutable()),
            new Cat('http://cat2.pl', 'creator', new \DateTimeImmutable())
        ]);

        $this->client->request('GET', '/api/cats');

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK, false);

        $catsList = json_decode($this->client->getResponse()->getContent(), true);
        self::assertCount(2, $catsList);
    }

    public function test_random_cat_without_send_token()
    {
        $this->client->request('GET', '/api/cats/random');

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_UNAUTHORIZED);
    }

    public function test_random_cat_with_invalid_token()
    {
        $headers = ['HTTP_AUTHORIZATION' => 'Bearer invalid_token'];
        $this->client->request('GET', '/api/cats/random', [], [], $headers);

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_FORBIDDEN);
    }

    public function test_random_cat_with_valid_token()
    {
        $token = $this->createAndLoginUser();

        $headers = ['HTTP_AUTHORIZATION' => 'Bearer ' . $token];
        $this->client->request('GET', '/api/cats/random', [], [], $headers);

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK);

        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('url', $content);
    }

    public function test_add_cat_without_send_token()
    {
        $this->client->request(
            'POST',
            '/api/cats',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['id' => 'nieznane', 'url' => self::CAT_URL])
        );

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_UNAUTHORIZED);
    }

    public function test_add_cat_with_invalid_token()
    {
        $this->client->request(
            'POST',
            '/api/cats',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer invalid_token'],
            json_encode(['id' => 'nieznane', 'url' => self::CAT_URL])
        );

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_FORBIDDEN);
    }

    public function test_add_cat_with_valid_token()
    {
        $token = $this->createAndLoginUser();

        $this->client->request(
            'POST',
            '/api/cats',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $token],
            json_encode(['id' => 'nieznane', 'url' => self::CAT_URL])
        );

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_CREATED);

        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('id', $content);
        self::assertArrayHasKey('url', $content);
        self::assertArrayHasKey('creator', $content);
        self::assertArrayHasKey('created', $content);
    }

    public function test_add_cat_without_send_url()
    {
        $token = $this->createAndLoginUser();

        $this->client->request(
            'POST',
            '/api/cats',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $token],
            json_encode(['id' => 'nieznane'])
        );

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_BAD_REQUEST);
    }

    public function test_add_cat_with_send_empty_url()
    {
        $token = $this->createAndLoginUser();

        $this->client->request(
            'POST',
            '/api/cats',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $token],
            json_encode(['id' => 'nieznane', 'url' => ''])
        );

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * @group current
     */
    public function test_change_url_not_existing_cat()
    {
        $token = $this->createAndLoginUser();

        $this->client->request(
            'PATCH',
            '/api/cats/15',
            [],
            [],
            ['CONTENT_TYPE' => 'application/merge-patch+json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $token],
            json_encode(['id' => 'nieznane', 'url' => self::CAT_URL])
        );

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_NOT_FOUND);
    }

    public function test_change_url_not_own_cat()
    {
        $cat = $this->catContext->addCats([
            new Cat(self::CAT_URL, 'not_my_cat_creator', new \DateTimeImmutable())
        ]);

        $token = $this->createAndLoginUser();

        $this->client->request(
            'PATCH',
            '/api/cats/' . $cat[0],
            [],
            [],
            ['CONTENT_TYPE' => 'application/merge-patch+json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $token],
            json_encode(['url' => self::CAT_URL])
        );

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_FORBIDDEN);
    }

    public function test_change_url_successfully()
    {
        $cat = $this->catContext->addCats([
            new Cat(self::CAT_URL, UserCredentialsContext::USERNAME, new \DateTimeImmutable())
        ]);

        $token = $this->createAndLoginUser();

        $this->client->request(
            'PATCH',
            '/api/cats/' . $cat[0],
            [],
            [],
            ['CONTENT_TYPE' => 'application/merge-patch+json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $token],
            json_encode(['url' => 'http://new-cat.pl'])
        );

        self::assertEquals(Codes::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    public function test_change_url_with_empty_url()
    {
        $cat = $this->catContext->addCats([
            new Cat(self::CAT_URL, UserCredentialsContext::USERNAME, new \DateTimeImmutable())
        ]);

        $token = $this->createAndLoginUser();

        $this->client->request(
            'PATCH',
            '/api/cats/' . $cat[0],
            [],
            [],
            ['CONTENT_TYPE' => 'application/merge-patch+json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $token],
            json_encode(['url' => ''])
        );

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_BAD_REQUEST);
    }

    public function test_remove_cat_not_existing_in_system()
    {
        $token = $this->createAndLoginUser();

        $this->client->request('DELETE', '/api/cats/99', [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]);

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_NOT_FOUND);
    }

    public function test_remove_not_own_cat()
    {
        $cat = $this->catContext->addCats([
            new Cat(self::CAT_URL, 'not_my_cat_creator', new \DateTimeImmutable())
        ]);

        $token = $this->createAndLoginUser();

        $this->client->request('DELETE', '/api/cats/' . $cat[0], [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]);

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_FORBIDDEN);
    }

    public function test_remove_cat()
    {
        $cat = $this->catContext->addCats([
            new Cat(self::CAT_URL, UserCredentialsContext::USERNAME, new \DateTimeImmutable())
        ]);

        $token = $this->createAndLoginUser();

        $this->client->request('DELETE', '/api/cats/' . $cat[0], [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]);

        self::assertEquals(Codes::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @param string $username
     * @param string $password
     * @return string
     */
    private function loginUserInTheSystem($username = UserCredentialsContext::USERNAME, $password = UserCredentialsContext::PASSWORD)
    {
        $this->client->request(
            'POST',
            '/api/token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['username' => $username, 'password' => $password])
        );

        self::assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK);
        $content = json_decode($this->client->getResponse()->getContent(), true);

        return $content['token'];
    }

    /**
     * @param string $username
     * @param string $password
     * @return mixed
     */
    private function createAndLoginUser($username = UserCredentialsContext::USERNAME, $password = UserCredentialsContext::PASSWORD)
    {
        $this->userCredentialsContext->createUserCredentialsInSystem($username, $password);

        $token = $this->loginUserInTheSystem($username, $password);
        return $token;
    }
}