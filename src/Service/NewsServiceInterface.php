<?php
/**
 * News service interface.
 */

namespace App\Service;

use App\Entity\News;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface NewsServiceInterface.
 */
interface NewsServiceInterface
{
    /**
     * Get paginated list of news.
     *
     * @param int       $page       Page number
     * @param User|null $author     Optional author filter
     * @param int|null  $categoryId Optional category ID filter
     * @param int|null  $tagId      Optional tag ID filter
     *
     * @return PaginationInterface<string, mixed> Paginated list of news
     */
    public function getPaginatedList(int $page, ?User $author, ?int $categoryId, ?int $tagId): PaginationInterface;

    /**
     * Save entity.
     *
     * @param News $news News entity
     */
    public function save(News $news): void;

    /**
     * Delete entity.
     *
     * @param News $news News entity
     */
    public function delete(News $news): void;

    /**
     * Get all news paginated list.
     *
     * @param int      $page       Page number
     * @param int|null $categoryId Optional category ID filter
     * @param int|null $tagId      Optional tag ID filter
     *
     * @return PaginationInterface<string, mixed> Paginated list of news
     */
    public function getAllPaginatedList(int $page, ?int $categoryId, ?int $tagId): PaginationInterface;
}
