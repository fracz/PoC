<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 17.09.16
 * Time: 13:45
 */

namespace AppBundle\Repository\Doctrine;

use AppBundle\Model\Cat;
use AppBundle\Repository\CatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineCatRepository implements CatRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * DoctrineCatRepository constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(Cat $cat)
    {
        $this->entityManager->persist($cat);
        $this->entityManager->flush();
    }

    public function remove(Cat $cat)
    {
        $this->entityManager->remove($cat);
        $this->entityManager->flush();
    }

    public function findById($id)
    {
        $repository = $this->entityManager->getRepository(Cat::class);

        return $repository->find($id);
    }

    public function getAllCatsToList()
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select('c')
            ->from('Model:Cat', 'c')
            ->orderBy('c.created', 'DESC');

        $query = $qb->getQuery();

        return array_map(function ($cat) {
            $cat['created'] = $cat['created']->getTimestamp();
            return $cat;
        }, $query->getArrayResult());
    }
}