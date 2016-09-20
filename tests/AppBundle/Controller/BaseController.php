<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 18.09.16
 * Time: 09:31
 */

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\PurgeDatabase;
use Tests\AssertJsonResponse;

abstract class BaseController extends WebTestCase
{
    use PurgeDatabase;
    use AssertJsonResponse;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->container = static::$kernel->getContainer();
        $this->purgeDatabase();
    }

    protected function clearEntityManager()
    {
        $this->container->get('doctrine.orm.entity_manager')->clear();
    }
}