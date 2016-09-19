<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 18.09.16
 * Time: 09:18
 */

namespace Tests\Context;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Repository\UserCredentialsRepository;
use AppBundle\Service\UserCredentialsFactory;

class UserCredentialsContext extends BaseContext
{
    const USERNAME = 'username';
    const PASSWORD = 'password';

    /**
     * @var UserCredentialsRepository
     */
    private $userCredentialsRepository;
    /**
     * @var UserCredentialsFactory
     */
    private $userCredentialsFactory;

    /**
     * UserCredentialsContext constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserCredentialsFactory $userCredentialsFactory
     * @param UserCredentialsRepository $userCredentialsRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserCredentialsFactory $userCredentialsFactory,
        UserCredentialsRepository $userCredentialsRepository
    ) {
        parent::__construct($entityManager);

        $this->userCredentialsFactory = $userCredentialsFactory;
        $this->userCredentialsRepository = $userCredentialsRepository;
    }

    public function createUserCredentialsInSystem($username, $password)
    {
        $userCredentials = $this->userCredentialsFactory->create($username, $password);
        $this->userCredentialsRepository->add($userCredentials);
        $this->entityManager->clear();
    }
}