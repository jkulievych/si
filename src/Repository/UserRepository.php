<?php
/**
 *  User repository.
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Save entity.
     *
     * @param User $user User entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(User $user): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Deletes a User entity and related News entities from the database.
     *
     * @param User $user The User entity to delete
     *
     * @throws \Exception If an error occurs during transaction rollback
     */
    public function deleteUserWithRelatedEntities(User $user): void
    {
        assert($this->_em instanceof EntityManager);
        $manyNews = $this->_em
            ->getRepository(\App\Entity\News::class)
            ->findBy(['author' => $user]);

        $this->_em->beginTransaction();

        try {
            foreach ($manyNews as $news) {
                $this->_em->remove($news);
            }

            $this->_em->remove($user);

            $this->_em->flush();
            $this->_em->commit();
        } catch (\Exception $e) {
            $this->_em->rollback();
            throw $e;
        }
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('user');
    }

    /**
     * Delete entity.
     *
     * @param User $user User entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(User $user): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($user);
        $this->_em->flush();
    }

    /**
     * Deletes related News entities associated with a User.
     *
     * @param User $user The User entity whose related News entities should be deleted
     *
     * @throws ORMException
     */
    private function deleteRelatedEntities(User $user): void
    {
        assert($this->_em instanceof EntityManager);
        $manyNews = $this->manyNews->getAuthor();

        foreach ($manyNews as $news) {
            $this->_em->remove($news);
        }
        $this->_em->flush();
    }
}
