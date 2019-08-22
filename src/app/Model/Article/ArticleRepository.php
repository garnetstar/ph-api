<?php
declare(strict_types=1);

namespace Model\Article;

use Doctrine\ORM\EntityRepository;
use Model\Exception\ArticleNotFoundException;

class ArticleRepository extends EntityRepository
{

	/**
	 * @param int $id
	 * @return Article
	 * @throws ArticleNotFoundException
	 */
	public function getByIdNotDeleted(int $id): Article
	{
		/** @var Article $article */
		$article = $this->find($id);
		if ($article === null) {
			throw ArticleNotFoundException::byIdNotDeleted($id);
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