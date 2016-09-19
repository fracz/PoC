<?php

namespace spec\AppBundle\Security;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use AppBundle\Security\CatVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use AppBundle\Model\Cat;
use AppBundle\Model\UserCredentials;

/**
 * Class CatVoterSpec
 * @package spec\AppBundle\Security
 * @mixin CatVoter
 */
class CatVoterSpec extends ObjectBehavior
{
    const USERNAME = 'username';

    function it_is_initializable()
    {
        $this->shouldHaveType(CatVoter::class);
    }

    function it_is_voter()
    {
        $this->shouldImplement(Voter::class);
    }

    function it_is_abstain_if_not_support_attribute_to_grant(
        TokenInterface $token,
        Cat $cat
    ) {
        $this->vote($token, $cat, ['not_support_attribute'])->shouldBe(Voter::ACCESS_ABSTAIN);
    }

    function it_is_abstain_if_support_attribute_to_grant_but_this_is_not_cat(
        TokenInterface $token
    ) {
        $this->vote($token, new \stdClass(), [CatVoter::CHANGE])->shouldBe(Voter::ACCESS_ABSTAIN);
    }

    function it_is_access_denied_if_token_not_has_user(
        TokenInterface $token,
        Cat $cat
    ) {
        $token->getUser()->willReturn(null);

        $this->vote($token, $cat, [CatVoter::CHANGE])->shouldBe(Voter::ACCESS_DENIED);
    }

    function it_is_access_denied_if_author_cat_is_different_than_user_logged(
        TokenInterface $token,
        Cat $cat,
        UserCredentials $userCredentials
    ) {
        $token->getUser()->willReturn($userCredentials);
        $userCredentials->getUsername()->willReturn(self::USERNAME);
        $cat->getCreator()->willReturn('NOT_THE_SAME_USERNAME');

        $this->vote($token, $cat, [CatVoter::CHANGE])->shouldBe(Voter::ACCESS_DENIED);
    }

    function it_is_access_granted(TokenInterface $token, Cat $cat, UserCredentials $userCredentials)
    {
        $token->getUser()->willReturn($userCredentials);
        $userCredentials->getUsername()->willReturn(self::USERNAME);
        $cat->getCreator()->willReturn(self::USERNAME);

        $this->vote($token, $cat, [CatVoter::CHANGE])->shouldBe(Voter::ACCESS_GRANTED);
    }
}
