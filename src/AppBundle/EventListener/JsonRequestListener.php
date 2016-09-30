<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 16.09.16
 * Time: 09:07
 */

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Utils\Codes;
use Symfony\Component\Security\Http\HttpUtils;
use AppBundle\Form\Type\LoginType;
use AppBundle\Form\Type\AddCatType;
use AppBundle\Form\Type\ChangeCatType;

class JsonRequestListener
{
    /**
     * @var HttpUtils
     */
    private $httpUtils;

    /**
     * JsonRequestListener constructor.
     * @param HttpUtils $httpUtils
     */
    public function __construct(HttpUtils $httpUtils)
    {
        $this->httpUtils = $httpUtils;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $hasBeenSubmited = in_array($request->getMethod(), array('PATCH', 'POST', 'PUT'), true);

        $isJson = (1 === preg_match('#application/(merge-patch\+)?json#', $request->headers->get('Content-Type')));
        if (!$hasBeenSubmited || !$isJson) {
            return;
        }

        $data = json_decode($request->getContent(), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $event->setResponse(new JsonResponse(["error" => "invalid JSON"], Codes::HTTP_BAD_REQUEST));
        }

        $request->request->add($data ? $this->packToFormName($request, $data) : []);
    }

    private function packToFormName(Request $request, array $data)
    {
        if ($this->httpUtils->checkRequestPath($request, '/api/token')) {
            return [LoginType::NAME => $data];
        }

        if ($this->httpUtils->checkRequestPath($request, '/api/cats')) {
            return [AddCatType::NAME => $data];
        }

        if ($this->httpUtils->checkRequestPath($request, '/api/cats/' . $request->attributes->get('id', 0))) {
            return [ChangeCatType::NAME => $data];
        }

        return $data;
    }

    public function ontKernelView(GetResponseForControllerResultEvent $event)
    {
        $controllerResult = $event->getControllerResult();
        $data = array_key_exists('data', $controllerResult) ? $controllerResult['data'] : null;
        $status = array_key_exists('status', $controllerResult) ? $controllerResult['status'] : Codes::HTTP_OK;

        $event->setResponse(new JsonResponse($data, $status));
    }
}