<?php

namespace AppBundle\Model;

class Cat
{
    private $id;
    private $url;
    private $creator;
    /**
     * @var \DateTimeInterface
     */
    private $created;

    /**
     * Cat constructor.
     * @param $url
     * @param $creator
     * @param \DateTimeInterface $created
     */
    public function __construct($url, $creator, \DateTimeInterface $created)
    {
        $this->guardMe($url, $creator);

        $this->url = $url;
        $this->creator = $creator;
        $this->created = $created;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getCreator()
    {
        return $this->creator;
    }

    private function guardMe($url, $creator)
    {
        if (!isset($url) || empty($url)) {
            throw new \InvalidArgumentException('Url must be');
        }

        if (!isset($creator) || empty($creator)) {
            throw new \InvalidArgumentException('Creator must be');
        }
    }

    public function getCreated()
    {
        return $this->created->getTimestamp();
    }

    public function changeUrl($url)
    {
        $this->url = $url;
    }

    public function getId()
    {
        return $this->id;
    }
}
