<?php


namespace App\Service;


use App\Entity\Samsung;
use App\Repository\SamsungRepository;
use Doctrine\DBAL\Exception;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SamsungService
{
    private $repository;
    private $serializer;

    public function __construct(SamsungRepository  $repository,SerializerInterface $serializer)
    {
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

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

    public function getNodeSerialized(int $id){
        $node = $this->getNode($id);
        return $this->serializer->serialize($node,'json');
    }

    /**
     * Finds all devices
     */
    public function showAll() {

        $samsungDevices = $this->repository->findAll();
        $data = [];

        foreach ($samsungDevices as $node) {
            $data[] = [
                'id' => $node->getId(),
                'parent_id' => $node->getParentId(),
                'name' => $node->getName(),
                'created_at' => $node->getCreatedAt()
            ];
        }

//        return $this->serializer->serialize($samsungDevices,'json');

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
        $node = $this->getNode($id);
        if(!empty($parent_id)){
            $node->setParentId($parent_id);
        }
        if(!empty($name)){
            $node->setName($name);
        }
        return $node;
    }

    public function newNode($name,$parent_id){
        $samsungDevice = new Samsung();
        $samsungDevice->setName($name);
        $samsungDevice->setParentId($parent_id);
        return $samsungDevice;
    }

}