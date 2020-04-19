<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaxRepository")
 * @ORM\Table(
 *  name="taxes",
 *  options={
 *      "comment":"Taxes list"
 *  }
 * )
 */
class Tax
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(
     *  type="string",
     *  length=20,
     *  options={
     *      "comment":"Tax code"
     *  }
     * )
     */
    private $code;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}