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
use Doctrine\MongoDB\Connection;
use Tests\AssertJsonResponse;

abstract class BaseController extends WebTestCase
{
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
        /** @var Connection $m */
        $m = $this->container->get('doctrine_mongodb.odm.default_connection');
        $m->dropDatabase($this->container->getParameter('mongodb.database.test'));
    }
}