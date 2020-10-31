<?php


namespace App\Service;


use App\Dto\EmployeeDto;
use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use App\Repository\SamsungRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

class EmployeeService
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager,EmployeeRepository $repository){
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function getEmployees(){
        $employees = $this->repository->findAll();
        $employeesArray = [];
        foreach ($employees as $employee){
            $employeesArray[] = $employee->toDto();
        }
        return $employeesArray;
    }

    public function getOneEmployee($id){
        $employee = $this->repository->findOneBy(['id' => $id]);
        $dto = new EmployeeDto();
        $dto->name = $employee->getName();
        $dto->id = $employee->getId();
        return $dto;
    }

    public function newEmployee($name){

        try{
            $this->entityManager->getConnection()->beginTransaction();
            $employee = new Employee();

            if (!empty($name)) {
                $employee->setName($name);
            }else{
                throw new Exception("Employee name is empty.");
            }
            $this->entityManager->persist($employee);
            $this->entityManager->flush();
            $this->entityManager->commit();
            return ["ok" => true]; // send object
        }catch (Exception $e){
            $this->entityManager->rollback();
            throw $e;
        }
    }

}