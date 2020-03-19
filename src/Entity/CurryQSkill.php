<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CurryQSkillRepository")
 */
class CurryQSkill
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
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CurryQSkillCategory", inversedBy="curryQSkills")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

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

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getCategory(): ?CurryQSkillCategory
    {
        return $this->category;
    }

    public function setCategory(?CurryQSkillCategory $category): self
    {
        $this->category = $category;

        return $this;
    }
}
