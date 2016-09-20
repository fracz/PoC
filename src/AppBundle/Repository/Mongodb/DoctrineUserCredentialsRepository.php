<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 20.09.16
 * Time: 10:16
 */

namespace AppBundle\Repository\Mongodb;

use Doctrine\ODM\MongoDB\DocumentManager;
use AppBundle\Model\UserCredentials;
use AppBundle\Repository\UserCredentialsRepository;

class DoctrineUserCredentialsRepository implements UserCredentialsRepository
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * DoctrineUserCredentialsRepository constructor.
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * @param $username
     * @return UserCredentials|null
     */
    public function findByUsername($username)
    {
        $repository = $this->documentManager->getRepository(UserCredentials::class);

        return $repository->findOneBy(['username' => $username]);
    }

    /**
     * @param UserCredentials $userCredentials
     */
    public function add(UserCredentials $userCredentials)
    {
        $this->documentManager->persist($userCredentials);
        $this->documentManager->flush();
    }
}