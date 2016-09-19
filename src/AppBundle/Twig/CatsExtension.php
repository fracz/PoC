<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 18.09.16
 * Time: 20:15
 */

namespace AppBundle\Twig;

use AppBundle\Repository\CatRepository;

class CatsExtension extends \Twig_Extension
{
    /**
     * @var CatRepository
     */
    private $catRepository;

    /**
     * CatsExtension constructor.
     * @param CatRepository $catRepository
     */
    public function __construct(CatRepository $catRepository)
    {
        $this->catRepository = $catRepository;
    }

    public function getName()
    {
        return 'cats';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'list_all_cats',
                [$this, 'listAllCats'],
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true
                ]
            )
        ];
    }

    public function listAllCats(\Twig_Environment $environment)
    {
        $cats = $this->catRepository->getAllCatsToList();

        $context = [
            'cats' => $cats
        ];

        return $environment->render('cat/list_cats.html.twig', $context);
    }
}