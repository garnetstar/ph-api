<?php
declare(strict_types=1);

namespace Model\Search;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use Model\Article\Article;

class AlgoliaSearchManager
{

	private const ARTICLE_INDEX = 'articles';

	/**
	 * @var SearchClient
	 */
	private $searchClient;

	public function __construct(SearchClient $searchClient)
	{
		 $this->searchClient = $searchClient;
	}

	public function saveArticle(Article $article): void
	{
		$this->getIndex()->saveObject(ArticleMapper::mapArticle($article));
	}

	private function getIndex(): SearchIndex
	{
		return $this->searchClient->initIndex(self::ARTICLE_INDEX);
	}
}
