<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CurryQEducationRepository")
 */
class CurryQEducation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $label;

    /**
     * @ORM\Column(type="smallint")
     */
    private $year_start;

    /**
     * @ORM\Column(type="smallint")
     */
    private $year_end;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getYearStart(): ?int
    {
        return $this->year_start;
    }

    public function setYearStart(int $year_start): self
    {
        $this->year_start = $year_start;

        return $this;
    }

    public function getYearEnd(): ?int
    {
        return $this->year_end;
    }

    public function setYearEnd(int $year_end): self
    {
        $this->year_end = $year_end;

        return $this;
    }
}
