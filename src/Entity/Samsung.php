<?php

namespace App\Entity;

use App\Repository\SamsungRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SamsungRepository::class)
 */
class Samsung
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $parent_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column (type="datetime")
     */
    private $created_at;


    public function __construct()
    {
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

    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }
}
