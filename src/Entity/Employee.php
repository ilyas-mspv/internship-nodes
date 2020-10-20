<?php

namespace App\Entity;

use App\Dto\EmployeeDto;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=EmployeeRepository::class)
 */
class Employee
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"employees","employee"})
     */
    private $id;

    /**
     * @ORM\Column (type="string", length=255)
     * @Groups ({"employees","employee"})
     */
    private $name;

    /**
     * @ORM\OneToMany (targetEntity="Work",mappedBy="employee_id")
     * @Groups ({"employee"})
     */
    private $nodes;

    /**
     * Employee constructor.
     */
    public function __construct()
    {
        $this->nodes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }


    /**
     * @return Collection|Samsung[]
     */
    public function getNodes(): Collection
    {
        return $this->nodes;
    }

    public function addNode(Samsung $samsung): self
    {
        if (!$this->nodes->contains($samsung)) {
            $this->nodes[] = $samsung;
            $samsung->addEmployee($this);
        }

        return $this;
    }

    public function removeNode(Samsung $samsung): self
    {
        if ($this->nodes->contains($samsung)) {
            $this->nodes->removeElement($samsung);
            $samsung->removeEmployee($this);
        }

        return $this;
    }


    public function toDto(){
        $dto = new EmployeeDto();
        $dto->name = $this->getName();
        $dto->id = $this->getId();
        return $dto;
    }
}
