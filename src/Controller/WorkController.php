<?php

namespace App\Controller;

use App\Entity\Work;
use App\Repository\EmployeeRepository;
use App\Repository\SamsungRepository;
use App\Repository\WorkRepository;
use App\Service\WorkService;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class WorkController extends AbstractController
{

    private $service;

    public function __construct(WorkService $service)
    {
        $this->service = $service;
    }

    /**
     * Все должности определенного работника
     * @Route ("/works/employee/{eid<\d+>}",name="get_employee_nodes")
     * @param $eid
     * @return Response
     */
    public function employeeNodes($eid){
        return $this->json($this->service->getEmployeeWorks($eid));
    }

    /**
     * @Route ("/works/node/{nid<\d+>}", name="get_node_employees")
     * @param $nid
     * @return Response
     */
    public function nodeEmployees(int $nid){
        return $this->json($this->service->getNodeEmployees($nid));
    }

    /**
     * @Route("/works", name="change_work",methods={"POST","PUT","DELETE"})
     * @param Request $request
     * @return JsonResponse
     * @throws ConnectionException
     * @throws NonUniqueResultException
     */
    public function changeWork(Request $request)
    {
        return $this->json(
            $this->service->changeWork(
                $request->request->get("node_id"),
                $request->request->get("employee_id"),
                $request->request->get("rate")
            )
        );
    }


}
