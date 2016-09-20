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

    /**
     * @param $username
     * @param $password
     * @param bool $clear
     * @return \AppBundle\Model\UserCredentials
     */
    public function createUserCredentialsInSystem($username, $password, $clear = false)
    {
        $userCredentials = $this->userCredentialsFactory->create($username, $password);
        $this->userCredentialsRepository->add($userCredentials);
        if ($clear) {
            $this->entityManager->clear();
        }

        return $userCredentials;
    }
}