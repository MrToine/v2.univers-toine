<?php

namespace App\Entity;

use App\Repository\ForumTopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForumTopicRepository::class)]
class ForumTopic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updateAt = null;

    #[ORM\Column(nullable: true)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'topic')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Member $author = null;

    #[ORM\Column(nullable: true)]
    private ?int $state = null;

    #[ORM\OneToMany(mappedBy: 'topic', targetEntity: ForumPost::class)]
    private Collection $post;

    #[ORM\ManyToOne(inversedBy: 'topic')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ForumForum $forum = null;

    #[ORM\OneToMany(mappedBy: 'topic', targetEntity: Reading::class)]
    private Collection $readings;

    public function __construct()
    {
        $this->createAt = new \DateTimeImmutable();
        $this->updateAt = new \DateTimeImmutable();
        $this->readings = new ArrayCollection();
    }

    #[ORM\PrePersist()]
    public function setUpdateValue()
    {
        $this->updateAt = new \DateTimeImmutable();
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

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeImmutable $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAuthor(): ?Member
    {
        return $this->author;
    }

    public function setAuthor(?Member $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(?int $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, ForumPost>
     */
    public function getPost(): Collection
    {
        return $this->post;
    }

    public function addPost(ForumPost $post): self
    {
        if (!$this->post->contains($post)) {
            $this->post->add($post);
            $post->setTopic($this);
        }

        return $this;
    }

    public function removePost(ForumPost $post): self
    {
        if ($this->post->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getTopic() === $this) {
                $post->setTopic(null);
            }
        }

        return $this;
    }

    public function getForum(): ?ForumForum
    {
        return $this->forum;
    }

    public function setForum(?ForumForum $forum): self
    {
        $this->forum = $forum;

        return $this;
    }

    /**
     * @return Collection<int, Reading>
     */
    public function getReadings(): Collection
    {
        return $this->readings;
    }

    public function addReading(Reading $reading): self
    {
        if (!$this->readings->contains($reading)) {
            $this->readings->add($reading);
            $reading->setTopic($this);
        }

        return $this;
    }

    public function removeReading(Reading $reading): self
    {
        if ($this->readings->removeElement($reading)) {
            // set the owning side to null (unless already changed)
            if ($reading->getTopic() === $this) {
                $reading->setTopic(null);
            }
        }

        return $this;
    }
}
