<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 18.09.16
 * Time: 09:18
 */

namespace Tests\Context;

use Doctrine\ODM\MongoDB\DocumentManager;
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
     * @param DocumentManager $documentManager
     * @param UserCredentialsFactory $userCredentialsFactory
     * @param UserCredentialsRepository $userCredentialsRepository
     */
    public function __construct(
        DocumentManager $documentManager,
        UserCredentialsFactory $userCredentialsFactory,
        UserCredentialsRepository $userCredentialsRepository
    ) {
        parent::__construct($documentManager);

        $this->userCredentialsFactory = $userCredentialsFactory;
        $this->userCredentialsRepository = $userCredentialsRepository;
    }

    public function createUserCredentialsInSystem($username, $password)
    {
        $userCredentials = $this->userCredentialsFactory->create($username, $password);
        $this->userCredentialsRepository->add($userCredentials);
        $this->documentManager->clear();
    }
}