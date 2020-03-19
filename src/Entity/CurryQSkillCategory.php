<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CurryQSkillCategoryRepository")
 */
class CurryQSkillCategory
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
    private $position;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CurryQSkill", mappedBy="category", orphanRemoval=true)
     */
    private $curryQSkills;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $slug;

    public function __construct()
    {
        $this->curryQSkills = new ArrayCollection();
    }

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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection|CurryQSkill[]
     */
    public function getCurryQSkills(): Collection
    {
        return $this->curryQSkills;
    }

    public function addCurryQSkill(CurryQSkill $curryQSkill): self
    {
        if (!$this->curryQSkills->contains($curryQSkill)) {
            $this->curryQSkills[] = $curryQSkill;
            $curryQSkill->setCategory($this);
        }

        return $this;
    }

    public function removeCurryQSkill(CurryQSkill $curryQSkill): self
    {
        if ($this->curryQSkills->contains($curryQSkill)) {
            $this->curryQSkills->removeElement($curryQSkill);
            // set the owning side to null (unless already changed)
            if ($curryQSkill->getCategory() === $this) {
                $curryQSkill->setCategory(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
