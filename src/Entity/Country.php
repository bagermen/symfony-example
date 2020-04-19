<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 * @ORM\Table(
 *  name="country",
 *  options={
 *      "comment":"Countries list"
 *  }
 * )
 */
class Country
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(
     *  type="string",
     *  length=4,
     *  columnDefinition="CHAR(4) NOT NULL",
     *  options={
     *      "comment":"Country code by ISO 3166-1"
     *  }
     * )
     */
    private $code;

    /**
     * @ORM\Column(
     *  type="string",
     *  length=255,
     *  nullable=false,
     *  options={
     *      "comment":"Country name"
     *  }
     * )
     */
    private $name;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

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
}