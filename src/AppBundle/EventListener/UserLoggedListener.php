<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 16.09.16
 * Time: 18:19
 */

namespace AppBundle\EventListener;

use Doctrine\ODM\MongoDB\DocumentManager;
use AppBundle\Event\UserLoggedEvent;
use AppBundle\Repository\UserCredentialsRepository;

class UserLoggedListener
{
    /**
     * @var DocumentManager
     */
    private $documentManager;
    /**
     * @var UserCredentialsRepository
     */
    private $userCredentialsRepository;

    /**
     * UserLoggedListener constructor.
     * @param DocumentManager $documentManager
     * @param UserCredentialsRepository $userCredentialsRepository
     */
    public function __construct(DocumentManager $documentManager, UserCredentialsRepository $userCredentialsRepository)
    {
        $this->documentManager = $documentManager;
        $this->userCredentialsRepository = $userCredentialsRepository;
    }

    public function onUserLogged(UserLoggedEvent $event)
    {
        $userCredentials = $this->userCredentialsRepository->findByUsername($event->getUsername());

        $userCredentials->loginSuccessfully($event->getLastLogin(), $event->getIp());
        $this->documentManager->flush($userCredentials);
    }
}