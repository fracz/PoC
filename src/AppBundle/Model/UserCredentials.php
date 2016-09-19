<?php

namespace AppBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

class UserCredentials implements UserInterface
{
    const DEFAULT_ROLE = 'ROLE_USER';

    protected $id;

    /**
     * @var
     */
    private $username;
    /**
     * @var
     */
    private $password;

    /**
     * @var \DateTimeInterface|null
     */
    private $lastLoginDate;

    /**
     * @var string|null
     */
    private $lastLoginIp;

    /**
     * UserCredentials constructor.
     * @param $username
     */
    public function __construct($username)
    {
        $this->username = $username;
    }

    public function getRoles()
    {
        return [self::DEFAULT_ROLE];
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
    }

    public function assignEncodedPassword($password)
    {
        $this->password = $password;
    }

    public function getLastLoginDate()
    {
        return $this->lastLoginDate;
    }

    public function getLastLoginIp()
    {
        return $this->lastLoginIp;
    }

    public function loginSuccessfully(\DateTimeInterface $date, $ip)
    {
        $this->lastLoginDate = $date;
        $this->lastLoginIp = $ip;
    }
}
