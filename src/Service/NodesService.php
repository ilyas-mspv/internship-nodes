<?php


namespace App\Service;


use App\Entity\Samsung;
use App\Repository\EmployeeRepository;
use App\Repository\SamsungRepository;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NodesService
{
    private $repository;
    private $serializer;
    private $entityManager;
    private $er;
    private $nr;

    public function __construct(EntityManagerInterface $entityManager,SamsungRepository $repository, SerializerInterface $serializer,EmployeeRepository $er, SamsungRepository $sr)
    {
        $this->repository = $repository;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->er = $er;
        $this->nr = $sr;
    }

    //todo: clean up code
    //      get rid of serializer

    /**
     * @param int $id
     * @return Samsung|null
     */
    public function getNode(int $id){
        if (!empty($this->repository)) {
            return $this->repository->findOneBy(['id'=>$id]);
        }else{
            throw new NotFoundHttpException("Entity not found.");
        }
    }

    public function getOneNode(int $id){
        return $this->getNode($id)->toDto();
    }

    /**
     * Finds all devices
     */
    public function showAll() {

        $samsungDevices = $this->repository->findAll();
        $data = [];

        foreach ($samsungDevices as $node) {
            $data[] = $node->toDto();
        }
        return $data;
    }

    public function showTree(){
        $samsungDevices = $this->repository->selectNodes();
        $items = array();

        foreach ($samsungDevices as $k => &$v) {
            $items[$v["id"]] = &$v;
        }

        foreach($samsungDevices as $k => &$v){
            if($v['parent_id'] && isset($items[$v['parent_id']])){
                $items[$v['parent_id']]['nodes'][] = &$v;
            }
        }

        foreach($samsungDevices as $k => &$v){
            if($v['parent_id'] && isset($items[$v['parent_id']])){
                unset($samsungDevices[$k]);
            }
        }
        return $items;
    }

    public function updateNode(int $id, $parent_id, $name){
        $this->entityManager->getConnection()->beginTransaction();
        try{
            $node = $this->getNode($id);
            if(!empty($parent_id))
                $node->setParentId($parent_id);
            else
                throw new \Exception("Parent Id shouldn't be empty.");

            if(!empty($name))
                $node->setName($name);
            else
                throw new \Exception("Name shouldn't be empty.");

            $this->entityManager->persist($node);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            return ['ok' => true];
        }catch (Exception $e){
            $this->entityManager->getConnection()->rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function newNode($name,$parent_id){
        $this->entityManager->getConnection()->beginTransaction();
        try{
            $samsungDevice = new Samsung();
            $samsungDevice->setName($name);
            $samsungDevice->setParentId($parent_id);
            $this->entityManager->persist($samsungDevice);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            return ['ok' => true];
        } catch (ConnectionException $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        } catch (\Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function removeNode($id){
        $this->entityManager->getConnection()->beginTransaction();
        try{
            $node = $this->getNode($id);
            $this->entityManager->remove($node);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            return ['ok'=>true];
        }catch (\Exception $e){
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }
    }

}