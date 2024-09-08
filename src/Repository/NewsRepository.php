<?php
/**
 *  News repository.
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\News;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\EntityManager;

/**
 * Class NewsRepository.
 */
class NewsRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    /**
     * Retrieves a QueryBuilder instance to query News entities filtered by category and/or tag.
     *
     * @param Category|null $category Optional category filter
     * @param Tag|null      $tag      Optional tag filter
     *
     * @return QueryBuilder The QueryBuilder instance
     */
    public function queryByFilters(?Category $category, ?Tag $tag): QueryBuilder
    {
        $qb = $this->createQueryBuilder('news')
            ->leftJoin('news.tags', 't')
            ->addSelect('t');

        if ($category instanceof Category) {
            $qb->andWhere('news.category = :category')
                ->setParameter('category', $category);
        }

        if ($tag instanceof Tag) {
            $qb->andWhere(':tag MEMBER OF news.tags')
                ->setParameter('tag', $tag);
        }

        return $qb;
    }

    /**
     * Retrieves a QueryBuilder instance to query News entities filtered by author, category, and/or tag.
     *
     * @param User|null     $author   Optional author filter
     * @param Category|null $category Optional category filter
     * @param Tag|null      $tag      Optional tag filter
     *
     * @return QueryBuilder The QueryBuilder instance
     */
    public function queryByAuthorAndFilters(?User $author, ?Category $category, ?Tag $tag): QueryBuilder
    {
        $qb = $this->createQueryBuilder('news')
            ->leftJoin('news.tags', 't')
            ->addSelect('t')
            ->where('news.author = :author')
            ->setParameter('author', $author);

        if ($category instanceof Category) {
            $qb->andWhere('news.category = :category')
                ->setParameter('category', $category);
        }

        if ($tag instanceof Tag) {
            $qb->andWhere(':tag MEMBER OF news.tags')
                ->setParameter('tag', $tag);
        }

        return $qb;
    }

    /**
     * Delete entity.
     *
     * @param News $news News entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(News $news): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($news);
        $this->_em->flush();
    }

    /**
     * Save entity.
     *
     * @param News $news News entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(News $news): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($news);
        $this->_em->flush();
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('news')
            ->leftJoin('news.category', 'category')
            ->leftJoin('news.tags', 'tags')
            ->addSelect('category', 'tags')
            ->orderBy('news.updatedAt', 'DESC');
    }

    /**
     * Retrieves an array of News entities ordered by rating in descending order.
     *
     * @return News[] An array of News entities
     */
    public function findTopRated(): array
    {
        return $this->createQueryBuilder('news')
            ->orderBy('news.rating', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Adds a News entity to the EntityManager.
     *
     * @param News $news  The News entity to add
     * @param bool $flush Whether to flush changes immediately
     */
    public function add(News $news, bool $flush = false): void
    {
        $this->_em->persist($news);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
