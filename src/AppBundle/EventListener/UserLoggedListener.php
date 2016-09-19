<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 16.09.16
 * Time: 18:19
 */

namespace AppBundle\EventListener;

use AppBundle\Event\UserLoggedEvent;
use AppBundle\Repository\UserCredentialsRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserLoggedListener
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserCredentialsRepository
     */
    private $userCredentialsRepository;

    /**
     * UserLoggedListener constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserCredentialsRepository $userCredentialsRepository
     */
    public function __construct(EntityManagerInterface $entityManager, UserCredentialsRepository $userCredentialsRepository)
    {
        $this->entityManager = $entityManager;
        $this->userCredentialsRepository = $userCredentialsRepository;
    }

    public function onUserLogged(UserLoggedEvent $event)
    {
        $userCredentials = $this->userCredentialsRepository->findByUsername($event->getUsername());

        $this->entityManager->transactional(function (EntityManagerInterface $em) use ($event, $userCredentials) {
            $userCredentials->loginSuccessfully($event->getLastLogin(), $event->getIp());
        });
    }
}