<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 16.09.16
 * Time: 19:02
 */

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use AppBundle\Repository\CatRepository;
use AppBundle\Form\Type\AddCatType;
use AppBundle\Form\Type\ChangeCatType;
use AppBundle\Form\AddCat;
use AppBundle\Form\ChangeCat;
use AppBundle\Utils\Codes;
use AppBundle\Model\Cat;
use AppBundle\Service\RandomCatGenerator;

/**
 * Class CatsController
 * @package AppBundle\Controller
 * @Route(service="poc.controller.cats")
 */
class CatsController
{
    /**
     * @var CatRepository
     */
    private $catRepository;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var EngineInterface
     */
    private $engine;
    /**
     * @var RandomCatGenerator
     */
    private $randomCatGenerator;

    /**
     * CatsController constructor.
     * @param EngineInterface $engine
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface $formFactory
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param CatRepository $catRepository
     * @param RandomCatGenerator $randomCatGenerator
     */
    public function __construct(
        EngineInterface $engine,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        CatRepository $catRepository,
        RandomCatGenerator $randomCatGenerator
    ) {
        $this->engine = $engine;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->catRepository = $catRepository;
        $this->randomCatGenerator = $randomCatGenerator;
    }

    /**
     * @Route("/cats-in-html", name="all_cats_html", methods={"GET"})
     */
    public function catsHtmlAction()
    {
        return $this->engine->renderResponse('cat/cats.html.twig');
    }

    /**
     * @Route("/cats", name="all_cats", methods={"GET"})
     * @return array
     */
    public function getCatsAction()
    {
        return [
            'data' => $this->catRepository->getAllCatsToList()
        ];
    }

    /**
     * @Route("/cats/random", name="random_cats", methods={"GET"})
     * @return array
     */
    public function randomAction()
    {
        return [
            'data' => ['url' => $this->randomCatGenerator->getCatUrl()]
        ];
    }

    /**
     * @param Request $request
     * @Route("/cats", name="add_cat", methods={"POST"})
     * @return array
     */
    public function addCatAction(Request $request)
    {
        $form = $this->formFactory->create(AddCatType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var AddCat $addCat */
            $addCat = $form->getData();

            $cat = new Cat($addCat->url, $this->tokenStorage->getToken()->getUser(), new \DateTimeImmutable());
            $this->catRepository->add($cat);

            return [
                'data' => [
                    'id' => $cat->getId(),
                    'url' => $cat->getUrl(),
                    'creator' => $cat->getCreator(),
                    'created' => $cat->getCreated()
                ],
                'status' => Codes::HTTP_CREATED
            ];
        }

        return [
            'data' => ['error' => 'invalid data'],
            'status' => Codes::HTTP_BAD_REQUEST
        ];
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     * @Route("/cats/{id}", name="change_cat_url", requirements={"id": "\d+"}, methods={"PATCH"})
     */
    public function changeCatUrlAction(Request $request, $id)
    {
        $cat = $this->getCatWithId($id);
        $this->checkPermissionForAction('change', $cat);

        $form = $this->formFactory->create(ChangeCatType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var ChangeCat $data */
            $data = $form->getData();
            $this->entityManager->transactional(function () use ($cat, $data) {
                $cat->changeUrl($data->url);
            });

            return [
                'status' => Codes::HTTP_NO_CONTENT
            ];
        }

        return [
            'data' => ['error' => 'invalid data'],
            'status' => Codes::HTTP_BAD_REQUEST
        ];
    }

    /**
     * @Route("/cats/{id}", name="delete_cat", requirements={"id": "\d+"}, methods={"DELETE"})
     * @return array
     */
    public function deleteCatAction( $id)
    {
        $cat = $this->getCatWithId($id);
        $this->checkPermissionForAction('delete', $cat);

        $this->catRepository->remove($cat);

        return [
            'status' => Codes::HTTP_NO_CONTENT
        ];
    }

    private function getCatWithId($id)
    {
        $cat = $this->catRepository->findById($id);

        if (!isset($cat)) {
            throw new NotFoundHttpException(sprintf("Not found cat with id: %s", $id));
        }

        return $cat;
    }

    private function checkPermissionForAction($action, Cat $cat)
    {
        if (!$this->authorizationChecker->isGranted($action, $cat)) {
            throw new AccessDeniedHttpException(sprintf('Not have permission to action: %s', $action));
        }
    }
}