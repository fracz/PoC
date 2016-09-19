<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 18.09.16
 * Time: 09:48
 */

namespace Tests\Context;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Repository\CatRepository;
use AppBundle\Model\Cat;

class CatContext extends BaseContext
{
    /**
     * @var CatRepository
     */
    private $catRepository;

    /**
     * CatContext constructor.
     * @param EntityManagerInterface $entityManager
     * @param CatRepository $catRepository
     */
    public function __construct(EntityManagerInterface $entityManager, CatRepository $catRepository)
    {
        parent::__construct($entityManager);

        $this->catRepository = $catRepository;
    }

    /**
     * @param Cat[] $cats
     * @return array
     */
    public function addCats(array $cats)
    {
        $ids = [];

        foreach ($cats as $cat) {
            $this->catRepository->add($cat);
            $ids[] = $cat->getId();
        }
        $this->entityManager->clear();

        return $ids;
    }
}