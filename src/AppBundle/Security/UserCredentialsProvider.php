<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 16.09.16
 * Time: 21:18
 */

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use AppBundle\Model\UserCredentials;
use AppBundle\Repository\UserCredentialsRepository;

class UserCredentialsProvider implements UserProviderInterface
{
    /**
     * @var UserCredentialsRepository
     */
    private $userCredentialsRepository;

    /**
     * UserCredentialsProvider constructor.
     * @param UserCredentialsRepository $userCredentialsRepository
     */
    public function __construct(UserCredentialsRepository $userCredentialsRepository)
    {
        $this->userCredentialsRepository = $userCredentialsRepository;
    }

    public function loadUserByUsername($username)
    {
        $userCredentials = $this->userCredentialsRepository->findByUsername($username);

        if (!isset($userCredentials)) {
            throw new UsernameNotFoundException(sprintf("Not found user %s", $username));
        }

        return $userCredentials;
    }

    public function refreshUser(UserInterface $user)
    {
        return $user;
//        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return $class === UserCredentials::class;
    }
}