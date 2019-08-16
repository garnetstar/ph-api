<?php
declare(strict_types=1);

namespace Model\Article;

use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{

	public function findAllOrderedByLastUpdate(): array
	{
		$qb = $this->getEntityManager()->createQueryBuilder();

		$qb->select('a.id, a.title')
			->from(Article::class, 'a')
			->where('a.deleted IS NULL')
			->orderBy('a.updated', 'DESC');

		return $qb->getQuery()->getArrayResult();
	}
}