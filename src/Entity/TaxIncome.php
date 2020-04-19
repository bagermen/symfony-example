<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaxIncomeRepository")
 * @ORM\Table(
 *  name="tax_income",
 *  indexes={
 *      @ORM\Index(name="tax_income_date_idx", columns={"date"})
 *  }
 * ),
 *  options={
 *      "comment":"tax transactios"
 *  }
 */
class TaxIncome
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
     *  onDelete="CASCADE"
     * )
     */
    private $tax;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\County")
     * @ORM\JoinColumn(
     *  name="county_id",
     *  nullable=false
     * )
     */
    private $county;

    /**
     * @ORM\Column(
     *  type="datetime",
     *  columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
     *  options={
     *      "comment":"Time when tax has arrived"
     *  }
     * )
     */
    private $date;

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

    public function getCounty(): ?County
    {
        return $this->county;
    }

    public function setCounty(?County $county): self
    {
        $this->county = $county;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
