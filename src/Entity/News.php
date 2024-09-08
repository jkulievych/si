<?php
/**
 * News entity.
 */

namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class News.
 */
#[ORM\Entity(repositoryClass: NewsRepository::class)]
#[ORM\Table(name: 'manyNews')]
class News
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'string', length: 64)]
    #[Gedmo\Slug(fields: ['title'])]
    private ?string $slug = null;

    #[ORM\ManyToOne(targetEntity: Category::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Category $category = null;

    /**
     * Tags.
     *
     * @var ArrayCollection<int, Tag>
     */
    #[Assert\Valid]
    #[ORM\ManyToMany(targetEntity: Tag::class, fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\JoinTable(name: 'manyNews_tags')]
    private $tags;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(User::class)]
    private ?User $author = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $rating = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $ratingCount = null;

    #[ORM\OneToMany(mappedBy: 'news', targetEntity: Comment::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $comments;

    /**
     * Constructor.
     * Initializes the collection of tags and comments.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * Get the ID of the news.
     *
     * @return int|null int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the title of the news.
     *
     * @return string|null string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the title of the news.
     *
     * @param string|null $title Title
     *
     * @return void void
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get the description of the news.
     *
     * @return string|null string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the description of the news.
     *
     * @param string|null $description Description
     *
     * @return void void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get the creation date of the news.
     *
     * @return \DateTimeImmutable|null date time immutable
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Set the creation date of the news.
     *
     * @param \DateTimeImmutable $createdAt Created at
     *
     * @return void void
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get the last update date of the news.
     *
     * @return \DateTimeImmutable|null Datetimeimmutable
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Set the last update date of the news.
     *
     * @param \DateTimeImmutable|null $updatedAt Updated at
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get the slug of the news.
     *
     * @return string|null string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Get the category of the news.
     *
     * @return Category|null category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Set the category of the news.
     *
     * @param Category|null $category Category
     *
     * @return $this this
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Getter for tags.
     *
     * @return Collection<int, Tag> Tags collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Add tag.
     *
     * @param Tag $tag Tag entity
     */
    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
    }

    /**
     * Remove tag.
     *
     * @param Tag $tag Tag entity
     */
    public function removeTag(Tag $tag): void
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get the author (user) of the news.
     *
     * @return User|null user
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Set the author (user) of the news.
     *
     * @param User|null $author Author
     *
     * @return $this this
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get the comment associated with the news.
     *
     * @return string|null string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Set the comment associated with the news.
     *
     * @param string|null $comment Comment
     *
     * @return $this this
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get the rating of the news.
     *
     * @return float|null float
     */
    public function getRating(): ?float
    {
        return $this->rating;
    }

    /**
     * Set the rating of the news.
     *
     * @param float|null $rating Rating
     *
     * @return $this this
     */
    public function setRating(?float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get the number of ratings received for the news.
     *
     * @return int|null int
     */
    public function getRatingCount(): ?int
    {
        return $this->ratingCount;
    }

    /**
     * Set the number of ratings received for the news.
     *
     * @param int|null $ratingCount Rating count
     *
     * @return $this this
     */
    public function setRatingCount(?int $ratingCount): self
    {
        $this->ratingCount = $ratingCount;

        return $this;
    }

    /**
     * Get comments.
     *
     * @return Collection<int, Comment> Comments collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * Add comment.
     *
     * @param Comment $comment Comment entity
     */
    public function addComment(Comment $comment): void
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setNews($this);
        }
    }

    /**
     * Remove comment.
     *
     * @param Comment $comment Comment entity
     */
    public function removeComment(Comment $comment): void
    {
        // Set the owning side to null (unless already changed)
        if ($this->comments->removeElement($comment) && $comment->getNews() === $this) {
            $comment->setNews(null);
        }
    }
}
