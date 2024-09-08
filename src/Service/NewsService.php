<?php
/**
 * News service.
 */

namespace App\Service;

use App\Entity\Comment;
use App\Entity\News;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\NewsRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;

/**
 * Class NewsService.
 */
class NewsService implements NewsServiceInterface
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param CommentRepository  $commentRepository  Repository for comments
     * @param CategoryRepository $categoryRepository Repository for categories
     * @param PaginatorInterface $paginator          Paginator service for pagination
     * @param TagRepository      $tagRepository      Repository for tags
     * @param NewsRepository     $newsRepository     Repository for news
     */
    public function __construct(private readonly CommentRepository $commentRepository, private readonly CategoryRepository $categoryRepository, private readonly PaginatorInterface $paginator, private readonly TagRepository $tagRepository, private readonly NewsRepository $newsRepository)
    {
    }

    /**
     * Get paginated list of news.
     *
     * @param int       $page       Page number
     * @param User|null $author     Optional author filter
     * @param int|null  $categoryId Optional category ID filter
     * @param int|null  $tagId      Optional tag ID filter
     *
     * @return PaginationInterface Pagination object
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPaginatedList(int $page, ?User $author, ?int $categoryId, ?int $tagId): PaginationInterface
    {
        $category = null !== $categoryId ? $this->categoryRepository->findOneById($categoryId) : null;
        $tag = null !== $tagId ? $this->tagRepository->findOneById($tagId) : null;

        return $this->paginator->paginate(
            $this->newsRepository->queryByAuthorAndFilters($author, $category, $tag),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param News $news News entity
     */
    public function save(News $news): void
    {
        $this->newsRepository->save($news);
    }

    /**
     * Delete entity.
     *
     * @param News $news News entity
     */
    public function delete(News $news): void
    {
        $this->newsRepository->delete($news);
    }

    /**
     * Get all news paginated list.
     *
     * @param int      $page       Page number
     * @param int|null $categoryId Optional category ID filter
     * @param int|null $tagId      Optional tag ID filter
     *
     * @return PaginationInterface Pagination object
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAllPaginatedList(int $page, ?int $categoryId, ?int $tagId): PaginationInterface
    {
        $category = null !== $categoryId ? $this->categoryRepository->findOneById($categoryId) : null;
        $tag = null !== $tagId ? $this->tagRepository->findOneById($tagId) : null;

        return $this->paginator->paginate(
            $this->newsRepository->queryByFilters($category, $tag),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save comment.
     *
     * @param Comment $comment Comment entity
     */
    public function comment(Comment $comment): void
    {
        $this->commentRepository->save($comment, true);
    }

    /**
     * Delete comment.
     *
     * @param Comment $comment Comment entity
     */
    public function deleteComment(Comment $comment)
    {
        $this->commentRepository->delete($comment, true);
    }
}
