<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaxCountyRepository")
 * @ORM\Table(
 *  name="tax_county",
 *  indexes={
 *      @ORM\Index(name="tax_county_idx", columns={"tax_code", "county_id"})
 *  },
 *  options={
 *      "comment":"Taxes per county"
 *  }
 * )
 */
class TaxCounty
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tax")
     * @ORM\JoinColumn(
     *  name="tax_code",
     *  referencedColumnName="code",
     *  onDelete="CASCADE",
     *  nullable=false
     * )
     */
    private $tax;

    /**
     * @ORM\Column(
     *  type="integer",
     *  options={
     *      "unsigned":true,
     *      "default":0,
     *      "comment":"Tax amount in cent"
     *  }
     * )
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\County")
     * @ORM\JoinColumn(
     *  name="county_id",
     *  onDelete="CASCADE",
     *  nullable=false
     * )
     */
    private $county;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTax(): ?Tax
    {
        return $this->tax;
    }

    public function setTax(?Tax $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCounty(): ?County
    {
        return $this->county;
    }

    public function setCounty(?County $county): self
    {
        $this->county = $county;

        return $this;
    }
}
