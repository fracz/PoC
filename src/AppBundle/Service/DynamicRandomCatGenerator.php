<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 20.09.16
 * Time: 17:50
 */

namespace AppBundle\Service;

class DynamicRandomCatGenerator implements RandomCatGenerator
{
    public function getCatUrl()
    {
        $cats = new \SimpleXMLElement(file_get_contents('http://thecatapi.com/api/images/get?format=xml&results_per_page=1'));
        $image = (array) $cats->data->images->image;

        return $image['url'];
    }
}