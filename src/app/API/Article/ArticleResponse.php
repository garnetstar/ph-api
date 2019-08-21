<?php
declare(strict_types=1);

namespace API\Article;

use Model\Article\Article;
use Model\DateTime\DateTime;

class ArticleResponse
{

	/**
	 * @var Article
	 */
	private $article;

	public function __construct(Article $article)
	{
		$this->article = $article;
	}

	public function toArray(): array
	{
		$deleted = $this->article->getDeleted();
		$updated = $this->article->getUpdated();

		return [
			'article_id' => $this->article->getId(),
			'title' => $this->article->getTitle(),
			'content' => $this->article->getContent(),
			'deleted' => $deleted ? $deleted->format(DateTime::DATE_TIME_FORMAT) : null,
			'updated' => $updated ? $updated->format(DateTime::DATE_TIME_FORMAT) : null,
		];
	}
}
