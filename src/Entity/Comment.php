<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 * @ORM\Table(name="comments")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1000)
     * @Assert\NotBlank(message="Това поле не може да бъде празно.")
     * @Assert\Length(
     *      min = 10,
     *      max = 1000,
     *      minMessage = "Коментара трябва да бъде поне {{ limit }} символа.",
     *      maxMessage = "Коментара трябва да бъде не по-дълъг от {{ limit }} символа."
     * )
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Photo", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $photo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $publishedOn;

    public function __construct()
    {
        $this->publishedOn = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function setPhoto(?Photo $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getPublishedOn(): ?\DateTimeImmutable
    {
        return $this->publishedOn;
    }

    public function setPublishedOn(\DateTimeImmutable $publishedOn): self
    {
        $this->publishedOn = $publishedOn;

        return $this;
    }
}
