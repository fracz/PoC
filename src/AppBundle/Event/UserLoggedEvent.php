<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 16.09.16
 * Time: 18:08
 */

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class UserLoggedEvent extends Event
{
    const NAME = 'user_logged';
    /**
     * @var
     */
    private $username;
    /**
     * @var
     */
    private $ip;
    /**
     * @var \DateTimeInterface
     */
    private $lastLogin;

    /**
     * UserLoggedEvent constructor.
     * @param $username
     * @param $ip
     * @param \DateTimeInterface $lastLogin
     */
    public function __construct($username, $ip, \DateTimeInterface $lastLogin)
    {
        $this->username = $username;
        $this->ip = $ip;
        $this->lastLogin = $lastLogin;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }
}