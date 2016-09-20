<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 20.09.16
 * Time: 17:49
 */

namespace AppBundle\Service;

class StaticRandomCatGenerator implements RandomCatGenerator
{
    private $cats = [
        'http://25.media.tumblr.com/tumblr_lkuabtBSXG1qbhtrto1_500.gif',
        'http://25.media.tumblr.com/tumblr_lwib2k877S1qbhms5o1_500.jpg',
        'http://24.media.tumblr.com/tumblr_m2opxsJvLB1qzi9p6o1_500.jpg',
        'http://25.media.tumblr.com/tumblr_lxm6r81HWV1r2rj8po1_1280.jpg',
        'http://24.media.tumblr.com/tumblr_m3bunbdOFe1r73wdao1_500.gif',
        'http://25.media.tumblr.com/tumblr_m3yx1nLfbs1ruheaoo1_1280.png',
        'http://24.media.tumblr.com/tumblr_m2m49nWcFn1qd477zo1_1280.jpg',
        'http://25.media.tumblr.com/tumblr_lzqyn5TWFt1qzex9io1_1280.jpg',
        'http://28.media.tumblr.com/tumblr_ltqx5rLwCM1r2rj8po1_1280.jpg',
        'http://28.media.tumblr.com/tumblr_lhoes17xuk1qfyzelo1_1280.jpg'
    ];

    /**
     * @return string
     */
    public function getCatUrl()
    {
        return $this->cats[array_rand($this->cats)];
    }
}