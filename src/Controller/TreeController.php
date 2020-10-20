<?php


namespace App\Controller;

use App\Service\NodesService;
use Doctrine\DBAL\ConnectionException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TreeController
 * @package App\Controller
 */
class TreeController extends AbstractController
{
    private $service;

    public function __construct(NodesService $service)
    {
        $this->service = $service;
    }


    /**
     * @Route("/tree/plain", name="nodes_plain")
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        return $this->json($this->service->showAll());
    }


    /**
     * @Route("/tree/all", name="nodes_all")
     * @param NodesService $service
     * @return JsonResponse
     */
    public function treeView(NodesService $service)
    {
        return $this->json($service->showTree());
    }


    /**
     * @Route ("/node/new", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws ConnectionException
     * @throws Exception
     */
    public function newNode(Request $request)
    {
        return $this->json(
            $this->service->newNode(
                $request->request->get("name"),
                $request->request->get("parent_id")
            )
        );
    }

    /**
     * @Route("/node/{id<\d+>}", name="get_node",methods={"GET"})
     * @param int $id
     * @return Response
     */
    public function getNode(int $id): Response
    {
        return $this->json($this->service->getOneNode($id));
    }

    /**
     * @Route("/node/{id<\d+>}", name="update_node", methods={"PUT"})
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function updateNode(Request $request, int $id): JsonResponse
    {
        return $this->json($this->service->updateNode($id, $request->request->get("parent_id"), $request->request->get("name")));
    }

    /**
     * @Route("/node/{id<\d+>}", name="delete_customer", methods={"DELETE"})
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function deleteNode(int $id): JsonResponse
    {
        return $this->json($this->service->removeNode($id));
    }


}