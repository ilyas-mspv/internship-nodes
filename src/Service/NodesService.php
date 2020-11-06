<?php


namespace App\Service;


use App\Entity\Samsung;
use App\Helper\PDF;
use App\Repository\EmployeeRepository;
use App\Repository\SamsungRepository;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

    /**
     * Outputs PDF file with all nodes in table view
     */
    public function showPdf() {
        $pdf = new PDF();
        $header = array('ID', 'ParentID', 'Name', 'CreatedAt');
        $samsungDevices = $this->repository->findAll();
        $data = [];
        foreach ($samsungDevices as $node) {
            $data[] = $node->toDto();
        }
        $pdf->SetFont('Arial','',14);
        $pdf->AddPage();
        $pdf->generateTable($header,$data);
        return $pdf->Output('D','table.pdf');
    }

    /**
     * Outputs Excel table with all nodes
     */
    public function showExcel() {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'ParentID');
        $sheet->setCellValue('C1', 'Name');
        $sheet->setCellValue('D1', 'CreatedAt');
        $styles = [
            'font'=>[
                'bold'=>true
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A1:D1')->applyFromArray($styles);

        $samsungDevices = $this->repository->findAll();

        $columns = ['A','B','C','D'];
        $row_count = 2;
        foreach ($samsungDevices as $node) {
            $sheet->setCellValue($columns[0].$row_count, $node->getId());
            $sheet->setCellValue($columns[1].$row_count, $node->getParentId());
            $sheet->setCellValue($columns[2].$row_count, $node->getName());
            $sheet->setCellValue($columns[3].$row_count, $node->getCreatedAt()->format("d.m.Y H:i:s"));
            $row_count++;
        }

        $writer = new Xlsx($spreadsheet);
        return function () use ($writer) {$writer->save('php://output');};
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
            var_dump($parent_id);
            var_dump($name);
            if(!is_null($parent_id) && !empty($name)){
                $node->setParentId($parent_id);
                $node->setName($name);
            }else{
                throw new \Exception("Input values shouldn't be empty.");
            }

            $this->entityManager->persist($node);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            return $node->toDto();
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
            return $samsungDevice->toDto();
        } catch (ConnectionException $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        } catch (\Exception $e){
            $this->entityManager->getConnection()->rollBack();
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