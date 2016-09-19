<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 17.09.16
 * Time: 16:31
 */

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use AppBundle\Model\Cat;
use AppBundle\Model\UserCredentials;

class CatVoter extends Voter
{
    const DELETE = 'delete';
    const CHANGE = 'change';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::DELETE, self::CHANGE])) {
            return false;
        }

        if (!$subject instanceof Cat) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserCredentials) {
            return false;
        }

        /** @var Cat $cat */
        $cat = $subject;

        return $cat->getCreator() === $user->getUsername();
    }
}