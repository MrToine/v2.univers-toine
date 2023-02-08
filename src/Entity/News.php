<?php

namespace App\Entity;

use App\Repository\NewsRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NewsRepository::class)]
class News
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Lenght(min: 4)]
    #[Assert\NotBlank()]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rewrited_title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column]
    private ?int $author_user_id = null;

    #[ORM\Column]
    private ?int $published = null;

    #[ORM\Column]
    private ?int $category_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $publishing_start_date = null;

    #[ORM\Column(nullable: true)]
    private ?int $publishing_end_date = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sources = null;

    #[ORM\Column(nullable: true)]
    private ?int $views_number = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $summary = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $author_custom_name = null;

    #[ORM\Column(length: 255)]
    private ?string $thumbnail = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $top_list_enabled = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotNull()]
    private ?\DateTimeInterface $creation_date = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $update_date = null;

    #[ORM\ManyToOne(inversedBy: 'news')]
    private ?NewsCats $category = null;

    
    public function __construct()
    {
        $this->creation_date = new \DateTimeImmutable();
        $this->update_date = new \DateTimeImmutable();
    }

    #[ORM\PrePersist()]
    public function setUpdateValue()
    {
        $this->update_date = new \DateTimeImmutable();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getRewritedTitle(): ?string
    {
        return $this->rewrited_title;
    }

    public function setRewritedTitle(?string $rewrited_title): self
    {
        $this->rewrited_title = $rewrited_title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthorUserId(): ?int
    {
        return $this->author_user_id;
    }

    public function setAuthorUserId(int $author_user_id): self
    {
        $this->author_user_id = $author_user_id;

        return $this;
    }

    public function getPublished(): ?int
    {
        return $this->published;
    }

    public function setPublished(int $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getcategoryId(): ?int
    {
        return $this->category_id;
    }

    public function setcategoryId(int $category_id): self
    {
        $this->category_id = $category_id;

        return $this;
    }

    public function getPublishingStartDate(): ?int
    {
        return $this->publishing_start_date;
    }

    public function setPublishingStartDate(int $publishing_start_date): self
    {
        $this->publishing_start_date = $publishing_start_date;

        return $this;
    }

    public function getPublishingEndDate(): ?int
    {
        return $this->publishing_end_date;
    }

    public function setPublishingEndDate(int $publishing_end_date): self
    {
        $this->publishing_end_date = $publishing_end_date;

        return $this;
    }

    public function getSources(): ?string
    {
        return $this->sources;
    }

    public function setSources(?string $sources): self
    {
        $this->sources = $sources;

        return $this;
    }

    public function getViewsNumber(): ?int
    {
        return $this->views_number;
    }

    public function setViewsNumber(?int $views_number): self
    {
        $this->views_number = $views_number;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getAuthorCustomName(): ?string
    {
        return $this->author_custom_name;
    }

    public function setAuthorCustomName(?string $author_custom_name): self
    {
        $this->author_custom_name = $author_custom_name;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getTopListEnabled(): ?string
    {
        return $this->top_list_enabled;
    }

    public function setTopListEnabled(string $top_list_enabled): self
    {
        $this->top_list_enabled = $top_list_enabled;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(\DateTimeInterface $creation_date): self
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeImmutable
    {
        return $this->update_date;
    }

    public function setUpdateDate(\DateTimeImmutable $update_date): self
    {
        $this->update_date = $update_date;

        return $this;
    }

    public function getCategory(): ?NewsCats
    {
        return $this->category;
    }

    public function setCategory(?NewsCats $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
