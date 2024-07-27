<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints\File;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: Types::STRING)]
    #[Groups("tricks")]
    private ?string $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups("tricks")]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups("tricks")]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("tricks")]
    private ?string $images = null;

    #[ORM\ManyToOne(inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("tricks")]
    private ?Group $groups = null;

    #[File]
    private ?UploadedFile $imageFile = null;

    #[ORM\Column]
    #[Groups("tricks")]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups("tricks")]
    private ?\DateTimeImmutable $editAt = null;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Comment::class)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    #[Groups("tricks")]
    private Collection $comments;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(mappedBy: 'tricks', targetEntity: Image::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $secondaryImages;

    /**
     * @var Collection<int, Video>
     */
    #[ORM\OneToMany(mappedBy: 'tricks', targetEntity: Video::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $videos;

    public function __construct()
    {
        $this->id = Ulid::generate();
        $this->comments = new ArrayCollection();
        $this->secondaryImages = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getImages(): ?string
    {
        return $this->images;
    }

    public function setImages(string $images): static
    {
        $this->images = $images;

        return $this;
    }

    public function getGroups(): ?Group
    {
        return $this->groups;
    }

    public function setGroups(?Group $groups): static
    {
        $this->groups = $groups;

        return $this;
    }

    public function getImageFile(): ?UploadedFile
    {
        return $this->imageFile;
    }

    public function setImageFile(?UploadedFile $imageFile): ?UploadedFile
    {
        return $this->imageFile = $imageFile;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getEditAt(): ?\DateTimeImmutable
    {
        return $this->editAt;
    }

    public function setEditAt(?\DateTimeImmutable $editAt): static
    {
        $this->editAt = $editAt;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        // set the owning side to null (unless already changed)
        if ($this->comments->removeElement($comment) && $comment->getTrick() === $this) {
            $comment->setTrick(null);
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getSecondaryImages(): Collection
    {
        return $this->secondaryImages;
    }

    public function addSecondaryImage(Image $secondaryImage): self
    {
        if (!$this->secondaryImages->contains($secondaryImage)) {
            $this->secondaryImages->add($secondaryImage);
            $secondaryImage->setTricks($this);
        }

        return $this;
    }

    public function removeSecondaryImage(Image $secondaryImage): self
    {
        // set the owning side to null (unless already changed)
        if ($this->secondaryImages->removeElement($secondaryImage) && $secondaryImage->getTricks() === $this) {
            $secondaryImage->setTricks(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): static
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setTricks($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): static
    {
        // set the owning side to null (unless already changed)
        if ($this->videos->removeElement($video) && $video->getTricks() === $this) {
            $video->setTricks(null);
        }

        return $this;
    }
}
