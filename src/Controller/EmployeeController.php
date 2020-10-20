<?php

namespace App\Controller;

use App\Service\EmployeeService;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    private $service;

    public function __construct(EmployeeService  $service)
    {
       $this->service = $service;
    }

    /**
     * @Route("/employees", name="employees")
     */
    public function index()
    {
        return $this->json($this->service->getEmployees());
    }

    /**
     * @Route ("/employee/new", name="new_employee",methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */

    public function newEmployee(Request $request)
    {
        return $this->json($this->service->newEmployee($request->request->get("name")));
    }

    /**
     * @Route("/employee/{id<\d+>}", name="get_employee",methods={"GET"})
     * @param int $id
     * @return Response
     */
    public function getEmployee(int $id): Response
    {
        return $this->json($this->service->getOneEmployee($id));
    }


}
