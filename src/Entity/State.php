<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Country;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StateRepository")
 * @ORM\Table(
 *  name="state",
 *  indexes={
 *      @ORM\Index(name="state_idx", columns={"name"})
 *  },
 *  options={
 *      "comment":"States list"
 *  }
 * )
 */
class State
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(
     *  name="name",
     *  type="string",
     *  length=255,
     *  nullable=false,
     *  options={
     *      "comment":"State name"
     *  }
     * )
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country")
     * @ORM\JoinColumn(
     *  name="country_code",
     *  referencedColumnName="code",
     *  onDelete="CASCADE",
     *  nullable=false,
     *  columnDefinition="CHAR(4) NOT NULL"
     * )
     */
    private $country;

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

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): self
    {
        $this->country = $country;

        return $this;
    }
}
