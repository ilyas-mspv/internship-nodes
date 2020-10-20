<?php


namespace App\Service;

use App\Entity\Work;
use App\Repository\EmployeeRepository;
use App\Repository\SamsungRepository;
use App\Repository\WorkRepository;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class WorkService
{

    private $repository;
    private $entityManager;
    private $er;
    private $nr;

    public function __construct(EntityManagerInterface $entityManager, WorkRepository $repository, EmployeeRepository $er, SamsungRepository $sr)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->er = $er;
        $this->nr = $sr;
    }


    public function getEmployeeWorks($eid){

        $works = $this->repository->findWorksByEmployeeId($eid);
        $employee = $works[0]->getEmployee();
        $dtoArray = ['name' => $employee->getName()];
        foreach ($works as $work){
            $item[] = $work->toEmployeeDto();
            $dtoArray["nodes"] = $item;
        }
        return $dtoArray;
    }


    //todo: сотрудников подразделения с дочерними элементами

    public function getNodeEmployees($nid){
        $works = $this->repository->findEmployeesByNodeId($nid);
        $node = $works[0]->getNode();
        $dtoArray = ['name' => $node->getName()];
        foreach ($works as $work){
            $item[] = $work->toNodeDto();
            $dtoArray["employees"] = $item;
        }
        //todo: iterate through all sub nodes

        return $dtoArray;
    }

    /**
     * @param  $nid
     * @param  $eid
     * @param  $rate
     * @return array
     * @throws ConnectionException|NonUniqueResultException
     */

    public function changeWork(int $nid, int $eid, float $rate)
    {
        $status = [];
        try {
            $this->entityManager->getConnection()->beginTransaction();
            $work = $this->repository->findWorkByNodeAndEmployeeIds($nid, $eid);
            if (!empty($work)) {
                if ($rate == 0) {
                    // delete work
                    $this->entityManager->remove($work);
                    $status['message'] = "Work deleted.";
                } else if ($rate != $work->getRate()) {
                    //update working rate
                    $rate_current = $this->repository->findRateSum($eid);
                    if($rate_current + $rate <= 1){
                        $work->setRate($rate);
                        $this->entityManager->persist($work);
                        $status['message'] = "Working rate has been updated.";
                    }else{
                        throw new \Exception("New rate sum must not exceed 1.00.");
                    }
                } else {
                    throw new \Exception("You can't update same working rate.");
                }
            } else {
                //new work
                $rate_current = $this->repository->findRateSum($eid);

                if($rate == 0)
                    throw new \Exception("You can't create zero rate work.");

                if ($rate_current + $rate <= 1) {

                    $employee = $this->er->findOneBy(['id' => $eid]);
                    $node = $this->nr->findOneBy(['id' => $nid]);

                    $workNew = new Work();
                    $workNew->setEmployee($employee);
                    $workNew->setNode($node);
                    $workNew->setRate($rate);

                    $this->entityManager->persist($workNew);
                    $status['message'] = "New work has been created.";
                }else{
                    throw new \Exception("Employee must not have working rate more than 1.00.");
                }
            }
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }

        return $status;
    }


}