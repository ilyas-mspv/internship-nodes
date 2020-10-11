<?php

namespace App\Repository;

use App\Entity\Samsung;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Samsung|null find($id, $lockMode = null, $lockVersion = null)
 * @method Samsung|null findOneBy(array $criteria, array $orderBy = null)
 * @method Samsung[]    findAll()
 * @method Samsung[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SamsungRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Samsung::class);
        $this->manager = $manager;
    }

    public function updateNode(Samsung $node)
    {
        $this->manager->persist($node);
        $this->manager->flush();
    }


    public function removeNode(Samsung $node)
    {
        $this->manager->remove($node);
        $this->manager->flush();
    }

    public function selectNodes(): array {
        $conn = $this->manager->getConnection();
        try {


            // добавить таблицу работников конкретных подразделений
            // связь с подразделением многие ко многим
            // нужно вести учет ставки работника

            // читать про сервисы симфони (контроллер - точка входа в прилодение)
            // логика в сервис
            // exceptions

            $stmt = $conn->prepare("SELECT itm.* FROM `samsung` as itm");
            $stmt->execute();
            return $stmt->fetchAllAssociative();
        } catch (\Doctrine\DBAL\Driver\Exception $e) {
            throw new \PDOException("Error while fetching data: ".$e->getMessage());
        } catch (Exception $e) {
            throw new \PDOException("Error while fetching data: ".$e->getMessage());
        }
    }
}
