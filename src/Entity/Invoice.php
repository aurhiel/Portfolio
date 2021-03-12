<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 */
class Invoice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $document_filename;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $sku;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_paid;

    /**
     * @ORM\ManyToOne(targetEntity=Quote::class, inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $quote;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDocumentFilename(): ?string
    {
        return $this->document_filename;
    }

    public function setDocumentFilename(?string $document_filename): self
    {
        $this->document_filename = $document_filename;

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getDatePaid(): ?\DateTimeInterface
    {
        return $this->date_paid;
    }

    public function setDatePaid(?\DateTimeInterface $date_paid): self
    {
        $this->date_paid = $date_paid;

        return $this;
    }

    public function getQuote(): ?Quote
    {
        return $this->quote;
    }

    public function setQuote(?Quote $quote): self
    {
        $this->quote = $quote;

        return $this;
    }
}
