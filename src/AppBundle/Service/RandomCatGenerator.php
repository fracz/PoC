<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 20.09.16
 * Time: 17:47
 */

namespace AppBundle\Service;

interface RandomCatGenerator
{
    /**
     * @return string
     */
    public function getCatUrl();
}