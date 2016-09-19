<?php

namespace AppBundle\Service;

use AppBundle\Model\UserCredentials;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCredentialsFactory
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * UserCredentialsFactory constructor.
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function create($username, $plainPassword)
    {
        $this->guardMe($username, $plainPassword);

        $userCredentials = new UserCredentials($username);
        $encodedPassword = $this->userPasswordEncoder->encodePassword($userCredentials, $plainPassword);
        $userCredentials->assignEncodedPassword($encodedPassword);

        return $userCredentials;
    }

    private function guardMe($username, $plainPassword)
    {
        if (!isset($username) || empty($username)) {
            throw new \InvalidArgumentException('Invalid username');
        }

        if (!isset($plainPassword) || empty($plainPassword)) {
            throw new \InvalidArgumentException('Invalid password');
        }
    }
}
