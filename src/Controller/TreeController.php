<?php


namespace App\Controller;

use App\Entity\Samsung;
use App\Repository\SamsungRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TreeController extends AbstractController
{
    private $treeRepository;

    public function __construct(SamsungRepository $samsungRepository)
    {
        $this->treeRepository = $samsungRepository;
    }


    /**
     * @Route("/", name="nodes")
     */
    public function index(): JsonResponse
    {

        $samsungDevices = $this->treeRepository->findAll();
        $data = [];

        foreach ($samsungDevices as $node) {
            $data[] = [
                'id' => $node->getId(),
                'parent_id' => $node->getParentId(),
                'name' => $node->getName(),
                'created_at' => $node->getCreatedAt()
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/tree", name="nodes_tree")
     */
    public function treeView()
    {

        $samsungDevices = $this->treeRepository->selectNodes();
        $items = array();

        foreach ($samsungDevices as $node) {
            $items[] = array(
                "id" => $node['id'],
                "parent_id" => $node["parent_id"],
                "name" => $node["name"],
                "hasChild" => $node["hasChild"]
            );
        }
        return new JsonResponse($items, Response::HTTP_OK);
//        return $this->render('base.html.twig', ['items'=>$items, 'current' => 0]);
    }




    /**
     * @Route ("/new", methods={"POST"})
     * @param Request $request
     * @return Response
     */

    public function newDevice(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $samsungDevice = new Samsung();
        $samsungDevice->setName("Galaxy Series");
        $samsungDevice->setParentId(3);

        $entityManager->persist($samsungDevice);
        $entityManager->flush();

        return new Response("Device id=" . $samsungDevice->getId());
    }

    /**
     * @Route("/{id<\d+>}", name="get_node",methods={"GET"})
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */

    public function getNode(int $id): JsonResponse
    {
        $node = $this->treeRepository->findOneBy(['id' => $id]);

        if (!$node)
            return new JsonResponse(['ok' => false], Response::HTTP_NOT_FOUND);

        return new JsonResponse([
            'id' => $node->getId(),
            'parent_id' => $node->getParentId(),
            'name' => $node->getName(),
            'created_at' => $node->getCreatedAt()
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="update_node", methods={"PUT"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateNode($id, Request $request): JsonResponse
    {
        $node = $this->treeRepository->findOneBy(['id' => $id]);

        if (!$node)
            return new JsonResponse(['ok' => false], Response::HTTP_NOT_FOUND);

        $data = json_decode($request->getContent(), true);

        empty($data['parent_id']) ? true : $node->setParentId($data['parent_id']);
        empty($data['name']) ? true : $node->setName($data['name']);

        $this->treeRepository->updateNode($node);

        return new JsonResponse(['ok' => true], Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="delete_customer", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        $customer = $this->treeRepository->find($id);

        $this->treeRepository->removeNode($customer);

        return new JsonResponse(['status' => 'Node deleted'], Response::HTTP_NO_CONTENT);
    }


}