<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 18.09.16
 * Time: 09:48
 */

namespace Tests\Context;

use Doctrine\ORM\EntityManagerInterface;

abstract class BaseContext
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * UserCredentialsContext constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }
}