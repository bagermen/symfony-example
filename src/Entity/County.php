<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountyRepository")
 * @ORM\Table(
 *  name="county",
 *  indexes={
 *      @ORM\Index(name="county_idx", columns={"name"})
 *  },
 *  options={
 *      "comment":"Counties list"
 *  }
 * )
 */
class County
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(
     *  type="string",
     *  length=255,
     *  nullable=false,
     *  options={
     *      "comment":"County name"
     *  }
     * )
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\State")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $state;

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

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): self
    {
        $this->state = $state;

        return $this;
    }
}
