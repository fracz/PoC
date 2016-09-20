<?php

namespace AppBundle\Model;

class Cat
{
    private $id;
    private $url;
    /**
     * @var UserCredentials
     */
    private $creator;
    /**
     * @var \DateTimeInterface
     */
    private $created;

    /**
     * Cat constructor.
     * @param $url
     * @param UserCredentials $creator
     * @param \DateTimeInterface $created
     */
    public function __construct($url, UserCredentials $creator, \DateTimeInterface $created)
    {
        $this->guardMe($url);

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
        return $this->creator->getUsername();
    }

    private function guardMe($url)
    {
        if (!isset($url) || empty($url)) {
            throw new \InvalidArgumentException('Url must be');
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
