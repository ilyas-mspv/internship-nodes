<?php


namespace App\Service;

use App\Dto\NodeDto;
use App\Entity\Work;
use App\Repository\EmployeeRepository;
use App\Repository\SamsungRepository;
use App\Repository\WorkRepository;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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


    public function getEmployeeWorks($eid)
    {

        $works = $this->repository->findWorksByEmployeeId($eid);
        if(empty($works))
            return  $this->er->findOneBy(['id'=>$eid])->toDto(); //todo: отдельная dto с пустым массивом
        $employee = $works[0]->getEmployee();
        $dtoArray = ['name' => $employee->getName()];
        foreach ($works as $work) {
            $item[] = $work->toEmployeeDto();
            $dtoArray["nodes"] = $item;
        }
        return $dtoArray;
    }

    public function getNodeEmployees($nid)
    {
        $works = $this->repository->findEmployeesByNodeId($nid);
        if(empty($works))
            return  $this->nr->findOneBy(['id'=>$nid])->toDto(); //todo: отдельная дто с массивом работников, если имеется

        $node = $works[0]->getNode()->toDto();
        $dtoNodes = ['id' => $node->id, 'parent' => $node->parentId, 'name' => $node->name, 'createdAt' => $node->createdAt];
        foreach ($works as $work) {
            $item[] = $work->toNodeDto();
            $dtoNodes['employees'] = $item;
        }
        $dtoNodes['subnodes'] = $this->findSubnodes($node->id);

        return $dtoNodes;
    }

    function findSubnodes($parent_id)
    {
        try {
            $sub_nodes = $this->repository->findNodeByParentId($parent_id);
            $result = [
                "id" => (int) $sub_nodes[0]["node_id"],
                "parentId" => (int) $sub_nodes[0]["node_parent_id"],
                "name"=> $sub_nodes[0]["node_name"],
                "createdAt"=> $sub_nodes[0]["node_created_at"]
            ];
            foreach ($sub_nodes as $sub_node) {
                $employee[] = [
                    'id' => (int) $sub_node['employee_id'],
                    'name' => $sub_node['employee_name'],
                    'rate' => (float) $sub_node['employee_rate']
                ];
                $result["employees"] = $employee;
            }

            $result['subnodes'] = $this->findSubnodes($sub_nodes[0]["node_id"]);
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param  $nid
     * @param  $eid
     * @param  $rate
     * @return array
     * @throws ConnectionException|NonUniqueResultException
     */

    //разнести по функциям
    public function changeWork(int $nid, int $eid, float $rate)
    {
        $status = [];
        try {
            $this->entityManager->getConnection()->beginTransaction();
            $work = $this->repository->findOneBy(['employee_id'=>$eid,'node_id'=>$nid]);
            if (!empty($work)) {
                if ($rate == 0) {
                    // delete work
                    $this->entityManager->remove($work);
                    $status['message'] = "Work deleted."; //в будущем: функциональные ответы
                } else if ($rate != $work->getRate()) {
                    //update working rate
                    $rate_current = $this->repository->findRateSum($eid);
                    if ($rate_current + $rate <= 1) {
                        $work->setRate($rate);
                        $this->entityManager->persist($work);
                        $status['message'] = "Working rate has been updated.";
                    } else {
                        throw new \Exception("New rate sum must not exceed 1.00.");
                    }
                } else {
                    throw new \Exception("You can't update same working rate."); //в будущем: отдельный класс исключений
                }
            } else {
                //new work
                $rate_current = $this->repository->findRateSum($eid);

                if ($rate == 0)
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
                } else {
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