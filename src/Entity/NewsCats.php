<?php

namespace App\Entity;

use App\Repository\NewsCatsRepository;

use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
//use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NewsCatsRepository::class)]
class NewsCats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $rewrited_name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $thumbnail = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: News::class)]
    private Collection $news;

    public function __construct()
    {
        $this->news = new ArrayCollection();
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

    public function getRewritedName(): ?string
    {
        return $this->rewrited_name;
    }

    public function setRewritedName(string $rewrited_name): self
    {
        $this->rewrited_name = $rewrited_name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function __toString() 
    {
        return $this->name;
    }

    /**
     * @return Collection<int, news>
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(news $news): self
    {
        if (!$this->news->contains($news)) {
            $this->news->add($news);
            $news->setCategory($this);
        }

        return $this;
    }

    public function removeNews(news $news): self
    {
        if ($this->news->removeElement($news)) {
            // set the owning side to null (unless already changed)
            if ($news->getCategory() === $this) {
                $news->setCategory(null);
            }
        }

        return $this;
    }
}
