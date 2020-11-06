<?php

namespace App\Entity;

use App\Dto\NodeDto;
use App\Repository\SamsungRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=SamsungRepository::class)
 */
class Samsung
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"employee"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"employee"})
     */
    private $parent_id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"employee"})
     */
    private $name;

    /**
     * @ORM\Column (type="datetime")
     * @Groups({"employee"})
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity="Work", mappedBy="node_id")
     */
    private $employees;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
        $this->created_at = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function setParentId(int $parent_id): self
    {
        $this->parent_id = $parent_id;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function addEmployee(Employee $employee): self
    {
        if (!$this->employees->contains($employee)) {
            $this->employees[] = $employee;
        }

        return $this;
    }

    public function removeEmployee(Employee $employee): self
    {
        if ($this->employees->contains($employee)) {
            $this->employees->removeElement($employee);
        }

        return $this;
    }

    /**
     * @return Collection|Employee[]
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }


    public function toDto(){
        $node = new NodeDto();
        $node->id = $this->getId();
        $node->name = $this->getName();
        $node->parentId = $this->getParentId();
        $node->createdAt = $this->getCreatedAt()->format("d.m.Y H:i:s");
        return $node;
    }

}
