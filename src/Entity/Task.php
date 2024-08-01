<?php

/**
 * This file is part of the [Your Project] package.
 *
 * (c) [Your Company or Your Name] <[your-email@example.com]>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a task object within the system.
 * Each task is linked to a specific category and has associated creation and update timestamps.
 *
 * @psalm-suppress MissingConstructor
 */
#[ORM\Table(name: 'tasks')]
#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    /**
     * The identifier for the task.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Timestamp when the task was created.
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Timestamp when the task was last updated.
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * The title of the task.
     */
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title = null;

    /**
     * The category associated with the task.
     */
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Category::class, fetch: 'EXTRA_LAZY')]
    private ?Category $category = null;

    #[ORM\ManyToOne]
    private ?User $author = null;

    /**
     * Retrieves the identifier of the task.
     *
     * @return int|null The identifier of the task or null if not set
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retrieves the creation timestamp of the task.
     *
     * @return \DateTimeImmutable|null The creation timestamp or null if not set
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Sets the creation timestamp of the task.
     *
     * @param \DateTimeImmutable|null $createdAt The new creation timestamp
     */
    public function setCreatedAt(?\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Retrieves the update timestamp of the task.
     *
     * @return \DateTimeImmutable|null The update timestamp or null if not set
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Sets the update timestamp of the task.
     *
     * @param \DateTimeImmutable|null $updatedAt The new update timestamp
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Retrieves the title of the task.
     *
     * @return string|null The title of the task or null if not set
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Sets the title of the task.
     *
     * @param string|null $title The new title of the task
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * Retrieves the category associated with the task.
     *
     * @return Category|null The category or null if not set
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Assigns a category to the task.
     *
     * @param Category|null $category The category to assign to the task
     *
     * @return static This instance for method chaining
     */
    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Retrieves the author of the task.
     *
     * @return User|null The author of the task or null if not set
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Sets the author of the task.
     *
     * @param User|null $author The author to set for the task
     *
     * @return static This instance for method chaining
     */
    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }
}
