<?php

namespace App\Entity;

use App\Dto\EmployeeDto;
use App\Dto\WorkEmployeeDto;
use App\Repository\WorkRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=WorkRepository::class)
 */
class Work
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Samsung::class, inversedBy="employees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $node_id;

    /**
     * @ORM\ManyToOne(targetEntity=Employee::class, inversedBy="nodes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $employee_id;

    /**
     * @ORM\Column (type="float")
     */
    private $rate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNode(): ?Samsung
    {
        return $this->node_id;
    }

    public function setNode(?Samsung $node_id): self
    {
        $this->node_id = $node_id;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee_id;
    }

    public function setEmployee(?Employee $employee_id): self
    {
        $this->employee_id = $employee_id;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function toEmployeeDto(){
        $dto = new WorkEmployeeDto();
        $dto->id = $this->getNode()->getId();
        $dto->name = $this->getNode()->getName();
        $dto->rate = $this->getRate();
        return $dto;
    }

    public function toNodeDto(){
        $dto = new WorkEmployeeDto();
        $dto->id = $this->getEmployee()->getId();
        $dto->name = $this->getEmployee()->getName();
        $dto->rate = $this->getRate();
        return $dto;
    }
}
