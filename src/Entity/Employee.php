<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @ORM\Entity(repositoryClass=EmployeeRepository::class)
 */
class Employee
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column (type="string", length=255)
     *
     */
    private $name;

    /**
     * @ORM\ManyToMany (targetEntity="Samsung",mappedBy="employees")
     */
    private $samsungs;

    /**
     * Employee constructor.
     */
    public function __construct()
    {
        $this->samsungs = new ArrayCollection();
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
    public function getSamsungs(): Collection
    {
        return $this->samsungs;
    }

    public function addSamsung(Samsung $samsung): self
    {
        if (!$this->samsungs->contains($samsung)) {
            $this->samsungs[] = $samsung;
            $samsung->addEmployee($this);
        }

        return $this;
    }

    public function removeSamsung(Samsung $samsung): self
    {
        if ($this->samsungs->contains($samsung)) {
            $this->samsungs->removeElement($samsung);
            $samsung->removeEmployee($this);
        }

        return $this;
    }

}
