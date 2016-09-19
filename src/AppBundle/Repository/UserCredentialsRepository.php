<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 15.09.16
 * Time: 20:12
 */

namespace AppBundle\Repository;

use AppBundle\Model\UserCredentials;

interface UserCredentialsRepository
{
    /**
     * @param $username
     * @return UserCredentials|null
     */
    public function findByUsername($username);

    /**
     * @param UserCredentials $userCredentials
     */
    public function add(UserCredentials $userCredentials);
}
