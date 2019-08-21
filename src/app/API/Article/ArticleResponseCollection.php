<?php
declare(strict_types=1);

namespace API\Article;

class ArticleResponseCollection
{

	/**
	 * @var ArticleResponse[]
	 */
	private $articles;

	public function __construct(array $articles)
	{
		foreach ($articles as $article) {
			$this->addArticle(new ArticleResponse($article));
		}
	}

	private function addArticle(ArticleResponse $article): void
	{
		$this->articles[] = $article;
	}

	public function toArray(): array
	{
		$articles = [];
		foreach ($this->articles as $article) {
			$articles[] = $article->toArray();
		}

		return $articles;
	}
}
