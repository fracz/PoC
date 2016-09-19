<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 16.09.16
 * Time: 12:45
 */

namespace AppBundle\Repository\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Model\UserCredentials;
use AppBundle\Repository\UserCredentialsRepository;

class DoctrineUserCredentialsRepository implements UserCredentialsRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * DoctrineUserCredentialsRepository constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $username
     * @return UserCredentials|null
     */
    public function findByUsername($username)
    {
        $repository = $this->entityManager->getRepository(UserCredentials::class);

        return $repository->findOneBy(['username' => $username]);
    }

    public function add(UserCredentials $userCredentials)
    {
        $this->entityManager->persist($userCredentials);
        $this->entityManager->flush();
    }
}