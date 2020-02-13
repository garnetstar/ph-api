<?php
declare(strict_types=1);

namespace Model\Article;

use Doctrine\ORM\EntityRepository;
use Model\Exception\ArticleNotFoundException;

class ArticleRepository extends EntityRepository
{

    /**
     * @param int $articleId
     * @return Article
     * @throws ArticleNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getByIdNotDeleted(int $articleId): Article
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('a')
            ->from(Article::class, 'a')
            ->where('a.deleted IS NULL')
            ->andWhere('a = :articleId')
            ->setParameter('articleId', $articleId);

        /** @var Article $article */
        $article = $qb->getQuery()->getOneOrNullResult();

        if ($article === null) {
            throw ArticleNotFoundException::byIdNotDeleted($articleId);
        }

        return $article;
    }

    /**
     * @return Article[]
     */
    public function findAllOrderedByLastUpdate(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('a')
            ->from(Article::class, 'a')
            ->where('a.deleted IS NULL')
            ->orderBy('a.updated', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $id
     * @throws ArticleNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function softDelete(int $id): void
    {
        $article = $this->getByIdNotDeleted($id);

        $article->setDeleted(new \DateTime());
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $titlePart
     * @return Article[]
     */
    public function findByTitleLike(string $titlePart): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('a')
            ->from(Article::class, 'a')
            ->where('a.deleted IS NULL')
            ->andWhere('a.title LIKE :title')
            ->orderBy('a.updated', 'DESC')
            ->setParameter('title', '%' . $titlePart . '%');

        return $qb->getQuery()->getResult();
    }
}
