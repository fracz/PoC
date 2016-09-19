<?php

namespace spec\AppBundle\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use AppBundle\Model\Cat;

/**
 * Class CatSpec
 * @package spec\AppBundle\Model
 * @mixin Cat
 */
class CatSpec extends ObjectBehavior
{
    const URL = 'url';
    const CREATOR = 'creator';
    const CREATED = '2015-05-05 20:20:20';

    function let()
    {
        $this->beConstructedWith(self::URL, self::CREATOR, $this->getCreatedDate());
    }

    function it_throw_exception_when_not_pass_url()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [null, self::CREATOR, $this->getCreatedDate()]);
    }

    function it_throw_exception_when_not_pass_creator()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [self::URL, null, $this->getCreatedDate()]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Cat::class);
    }

    function it_has_url()
    {
        $this->getUrl()->shouldBe(self::URL);
    }

    function it_has_creator()
    {
        $this->getCreator()->shouldBe(self::CREATOR);
    }

    function it_has_date_created_in_unix_timestamp()
    {
        $dateCreated = $this->getCreatedDate();

        $this->getCreated()->shouldBe($dateCreated->getTimestamp());
    }

    function it_can_change_url()
    {
        $newUrl = 'new_url';

        $this->changeUrl($newUrl);

        $this->getUrl()->shouldBe($newUrl);
    }

    private function getCreatedDate()
    {
        return new \DateTimeImmutable(self::CREATED);
    }
}
