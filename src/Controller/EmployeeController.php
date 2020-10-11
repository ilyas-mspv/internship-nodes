<?php

namespace App\Controller;

use App\Entity\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Flex\Response;

class EmployeeController extends AbstractController
{

    private $repository;

//    public function __construct(EmployeeRepository $repository)
//    {
//        $this->repository = $repository;
//    }
    /**
     * @Route("/employees", name="employee")
     */
    public function index()
    {
        $employees = $this->repository->findAll();
        $data = [];
        foreach ($employees as $employee) {
            $data[] = [
                'id'=>$employee->getId(),
                'name'=>$employee->getName()
            ];
        }
        return new JsonResponse($data);
    }
}
