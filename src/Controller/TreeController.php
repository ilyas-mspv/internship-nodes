<?php


namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Samsung;
use App\Repository\SamsungRepository;
use App\Service\SamsungService;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @param SamsungService $service
     * @return JsonResponse
     */
    public function index(SamsungService $service): Response
    {
        return new Response($service->showAll());
    }


    /**
     * @Route("/tree", name="nodes_tree")
     * @param SamsungService $service
     * @return JsonResponse
     */
    public function treeView(SamsungService $service)
    {
        return new JsonResponse($service->showTree(), Response::HTTP_OK);
    }


    /**
     * @Route("/testRelation", name="nodes")
     * @return Response
     */
    public function testRelation(){

        $node = $this->treeRepository->find(7);
        $employees = $node->getEmployees();


        return new Response(var_dump($employees));
    }

    /**
     * @Route ("/new", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SamsungService $service
     * @return Response
     * @throws ConnectionException
     * @throws Exception
     */
    public function newNode(Request $request,EntityManagerInterface $entityManager, SamsungService $service)
    {

        // TODO почитать про транзакции в  entity manager

        $entityManager->getConnection()->beginTransaction();
        try{
            $samsungDevice = $service->newNode(
                $request->request->get("name"),
                $request->request->get("parent_id")
            );

            $entityManager->persist($samsungDevice);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return new JsonResponse(['ok'=>true, 'id' => $samsungDevice->getId()]);
        } catch (ConnectionException $e) {
            $entityManager->getConnection()->rollBack();
            throw $e;
        } catch (\Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @Route("/{id<\d+>}", name="get_node",methods={"GET"})
     * @param SamsungService $service
     * @param int $id
     * @return Response
     */
    public function getNode(SamsungService $service, int $id): Response
    {
        $node = $service->getNodeSerialized($id);
        return new Response($node,Response::HTTP_OK,['Content-Type'=>'application/json']);
    }

    /**
     * @Route("/{id<\d+>}", name="update_node", methods={"PUT"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SamsungService $service
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function updateNode(Request $request,EntityManagerInterface $entityManager,SamsungService $service,int $id): JsonResponse
    {
        try{
            $parent_id = $request->get("parent_id");
            $name = $request->get("name");

            $node = $service->updateNode($id,$parent_id,$name);

            $entityManager->persist($node);
            $entityManager->flush($node);

            return new JsonResponse(['ok' => true], Response::HTTP_OK);
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @Route("/{id<\d+>}", name="delete_customer", methods={"DELETE"})
     * @param EntityManagerInterface $entityManager
     * @param SamsungService $service
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function deleteNode(EntityManagerInterface $entityManager,SamsungService $service, int $id): JsonResponse
    {
        try{
            $node = $service->getNode($id);
            $entityManager->remove($node);
            $entityManager->flush();
            return new JsonResponse(['status' => 'Node deleted'], Response::HTTP_OK);
        }catch (\Exception $e){
            throw new Exception($e->getMessage());
        }
    }


}