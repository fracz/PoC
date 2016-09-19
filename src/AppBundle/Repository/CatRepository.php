<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 17.09.16
 * Time: 13:42
 */

namespace AppBundle\Repository;

use AppBundle\Model\Cat;

interface CatRepository
{
    /**
     * @param Cat $cat
     */
    public function add(Cat $cat);

    /**
     * @param Cat $cat
     */
    public function remove(Cat $cat);

    /**
     * @param $id
     * @return Cat|null
     */
    public function findById($id);

    /**
     * @return array
     */
    public function getAllCatsToList();
}