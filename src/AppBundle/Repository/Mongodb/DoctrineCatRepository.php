<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 20.09.16
 * Time: 10:11
 */

namespace AppBundle\Repository\Mongodb;

use Doctrine\ODM\MongoDB\DocumentManager;
use AppBundle\Model\Cat;
use AppBundle\Repository\CatRepository;

class DoctrineCatRepository implements CatRepository
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * DoctrineCatRepository constructor.
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function add(Cat $cat)
    {
        $this->documentManager->persist($cat);
        $this->documentManager->flush();
    }

    public function remove(Cat $cat)
    {
        $this->documentManager->remove($cat);
        $this->documentManager->flush();
    }

    public function findById($id)
    {
        $repository = $this->documentManager->getRepository(Cat::class);

        return $repository->find($id);
    }

    public function getAllCatsToList()
    {
        $qb = $this->documentManager->createQueryBuilder('Model:Cat');

        $qb->sort('created', 'DESC');
        $query = $qb->getQuery();

        $listCats = [];
        /** @var Cat $cat */
        foreach ($query->getIterator() as $cat) {
            $listCats[] = [
                'id' => $cat->getId(),
                'created' => $cat->getCreated(),
                'url' => $cat->getUrl(),
                'creator' => $cat->getCreator()
            ];
        }

        return $listCats;
    }
}