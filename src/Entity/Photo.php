<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhotoRepository")
 * @ORM\Table(name="photos")
 */
class Photo
{

    public const MAX_COMMENTS = 10;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="photos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $uploadedOn;

    /**
     * @Assert\NotBlank(message="Моля прикачете желаната снимка")
     * @Assert\Image(
     *     maxSize="4M",
     *     maxSizeMessage="Файлът е прекалено голям ({{ size }} {{ suffix }}). Максимално позволения размер е {{ limit }} {{ suffix }}.",
     *     mimeTypesMessage="Позволени са само снимкови формати. Избраният файл е {{ type }}.",
     *     disallowEmptyMessage="Файлът не може да бъде празен.",
     *     notReadableMessage="Файлът не може да бъде прочетен.",
     *     uploadErrorMessage="Неизвестна грешка възникна при качването на файла. Моля опитайте отново."
     * )
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="photo", orphanRemoval=true)
     * @ORM\OrderBy({"publishedOn" = "DESC"})
     */
    private $comments;

    public function __construct()
    {
        $this->uploadedOn = new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUploadedOn(): ?\DateTimeImmutable
    {
        return $this->uploadedOn;
    }

    public function setUploadedOn(\DateTimeImmutable $uploadedOn): self
    {
        $this->uploadedOn = $uploadedOn;

        return $this;
    }

    public function getImage(): ?UploadedFile
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPhoto($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPhoto() === $this) {
                $comment->setPhoto(null);
            }
        }

        return $this;
    }
}
