<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 18.09.16
 * Time: 08:26
 */

namespace Tests;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;

trait PurgeDatabase
{
    protected function purgeDatabase()
    {
        $orm = $this->container->get('doctrine.orm.entity_manager');
        $purger = new ORMPurger($orm);
        $purger->purge();
        $orm->clear();
    }
}