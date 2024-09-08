<?php

/**
 * Comment repository.
 */

/**
 * This file is part of the "Your Project Name".
 *
 * (c) Your Name <your.email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class CommentRepository.
 *
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry The registry manager
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * Save a comment entity.
     *
     * @param Comment $comment The comment entity to save
     * @param bool    $flush   Whether to flush the changes (default: false)
     */
    public function save(Comment $comment, bool $flush = false): void
    {
        $this->_em->persist($comment);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Delete a comment entity.
     *
     * @param Comment $comment The comment entity to delete
     * @param bool    $flush   Whether to flush the changes (default: false)
     */
    public function delete(Comment $comment, bool $flush = false): void
    {
        $this->_em->remove($comment);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Find all comments by news.
     *
     * @param int $newsId The ID of the news entity
     *
     * @return Comment[] An array of comment entities
     */
    public function findByNews(int $newsId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.news = :newsId')
            ->setParameter('newsId', $newsId)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
