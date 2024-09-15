<?php
/**
 * Comment entity.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\CommentRepository;

/**
 * Class Comment.
 */
#[ORM\Table(name: 'comments')]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Comment content.
     */
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 5000)]
    private ?string $content = null;

    /**
     * Comment author's email.
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    /**
     * Related news entity.
     */
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: News::class, inversedBy: 'comments')]
    private ?News $news = null;

    /**
     * Get the comment ID.
     *
     * @return int|null The ID of the comment
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the comment content.
     *
     * @return string|null The content of the comment
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Set the comment content.
     *
     * @param string $content The content of the comment
     * @return self
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the email of the comment author.
     *
     * @return string|null The email of the author
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the email of the comment author.
     *
     * @param string $email The email of the author
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the associated news.
     *
     * @return News|null The related news entity
     */
    public function getNews(): ?News
    {
        return $this->news;
    }

    /**
     * Set the associated news.
     *
     * @param News|null $news The related news entity
     * @return self
     */
    public function setNews(?News $news): self
    {
        $this->news = $news;

        return $this;
    }
}
