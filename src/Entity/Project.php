<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_long;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $date;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $illustration_filename;

    /**
     * @ORM\ManyToMany(targetEntity=ProjectSpec::class)
     */
    private $specs;

    /**
     * @ORM\ManyToMany(targetEntity=Image::class)
     */
    private $screenshots;

    public function __construct()
    {
        $this->specs = new ArrayCollection();
        $this->screenshots = new ArrayCollection();
    }

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

    public function getNameLong(): ?string
    {
        return $this->name_long;
    }

    public function setNameLong(string $name_long): self
    {
        $this->name_long = $name_long;

        return $this;
    }

    public function getSlug()
    {
        $slugger = new Slugify();
        return $slugger->slugify($this->name);
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getIllustrationFilename(): ?string
    {
        return $this->illustration_filename;
    }

    public function setIllustrationFilename(string $illustration_filename): self
    {
        $this->illustration_filename = $illustration_filename;

        return $this;
    }

    /**
     * @return Collection|ProjectSpec[]
     */
    public function getSpecs(): Collection
    {
        return $this->specs;
    }

    public function addSpec(ProjectSpec $spec): self
    {
        if (!$this->specs->contains($spec)) {
            $this->specs[] = $spec;
        }

        return $this;
    }

    public function removeSpec(ProjectSpec $spec): self
    {
        $this->specs->removeElement($spec);

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getScreenshots(): Collection
    {
        return $this->screenshots;
    }

    public function addScreenshot(Image $screenshot): self
    {
        if (!$this->screenshots->contains($screenshot)) {
            $this->screenshots[] = $screenshot;
        }

        return $this;
    }

    public function removeScreenshot(Image $screenshot): self
    {
        $this->screenshots->removeElement($screenshot);

        return $this;
    }
}
