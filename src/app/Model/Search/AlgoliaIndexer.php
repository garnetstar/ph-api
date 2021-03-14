<?php
declare(strict_types=1);

namespace Model\Search;

use Algolia\AlgoliaSearch\SearchClient;
use Doctrine\ORM\EntityManager;
use Model\Article\Article;
use Model\Article\ArticleRepository;

class AlgoliaIndexer
{

    /**
     * @var SearchClient
     */
    private $searchClient;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(SearchClient $searchClient, EntityManager $entityManager, string $name = null)
    {
        $this->searchClient = $searchClient;
        $this->entityManager = $entityManager;
    }

    public function reindexAll(): int
    {
        /** @var ArticleRepository $articleRepository */
        $articleRepository = $this->entityManager->getRepository(Article::class);

        $articles = $articleRepository->findBy(
            [
                'deleted' => null,
            ]
        );

        $articlesBundle = [];
        $index = $this->searchClient->initIndex('articles');
        /** @var Article $article */
        foreach ($articles as $article) {
            $articlesBundle[] = [
                'objectID' => $article->getId(),
                'title' => $article->getTitle(),
                'content' => $article->getContent(),
            ];
        }

        $index->replaceAllObjects(
            $articlesBundle,
            [
                'safe' => true,
            ]
        );

        return count($articlesBundle);
    }
}
