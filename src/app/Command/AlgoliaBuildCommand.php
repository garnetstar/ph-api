<?php
declare(strict_types=1);

namespace Command;

use Algolia\AlgoliaSearch\SearchClient;
use Doctrine\ORM\EntityManager;
use Model\Article\Article;
use Model\Article\ArticleRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AlgoliaBuildCommand extends Command
{


	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @param EntityManager $entityManager
	 * @param string|null $name
	 */
	public function __construct(EntityManager $entityManager, string $name = null)
	{
		$this->entityManager = $entityManager;

		parent::__construct($name);
	}

	protected function configure()
	{
		$this->setName('app:algoliaBuild')
			->setDescription('Build index in Algolia');
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int|void
	 * @throws \Algolia\AlgoliaSearch\Exceptions\MissingObjectId
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$client = SearchClient::create(
			'SV32PVGG9Q',
			'8046172157d880e5236ef465b92ddd5f'
		);

		/** @var ArticleRepository $articleRepository */
		$articleRepository = $this->entityManager->getRepository(Article::class);

		$articles = $articleRepository->findAll();
		$articlesBundle = [];
		$index = $client->initIndex('articles');
		/** @var Article $article */
		foreach ($articles as $article) {
			$articlesBundle[] = [
				'objectID' => $article->getId(),
				'title' => $article->getTitle(),
				'content' => $article->getContent(),
			];
		}

		$index->saveObjects($articlesBundle);
	}
}
