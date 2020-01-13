<?php
declare(strict_types=1);

namespace Controllers;

use API\Article\ArticleResponse;
use API\Article\ArticleResponseCollection;
use Doctrine\ORM\EntityManager;
use Model\Article\Article;
use Model\Article\ArticleRepository;
use Model\Exception\ArticleNotFoundException;
use Nette\Database\Connection;
use Nette\Database\Context;
use Slim\Http\Request;
use Slim\Http\Response;

class ArticleController
{

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var ArticleRepository
	 */
	private $articleRepository;

	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->articleRepository = $this->entityManager->getRepository(Article::class);
	}

	public function get(Request $request, Response $response, $args): Response
	{
		if (isset($args['id'])) {

			try {
				$article = $this->articleRepository->getByIdNotDeleted((int)$args['id']);
			} catch (ArticleNotFoundException $e) {
				return $response->withStatus(404)->write('Not found.');
			}

			return $response->withJson((new ArticleResponse($article))->toArray());
		}

		$allArticles = $this->articleRepository->findAllOrderedByLastUpdate();

		$articleResponse = new ArticleResponseCollection($allArticles);

		return $response->withJson($articleResponse->toArray());
	}

	public function put(Request $request, Response $response, $args): Response
	{
		try {
			$data = json_decode($request->getBody()->getContents());

			$article = new Article($data->title, '');

			$this->entityManager->persist($article);
			$this->entityManager->flush();

			return $response->withJson(['state' => 'ok', 'article_id' => $article->getId()]);
		} catch (\Exception $e) {
			return $response->withStatus(500)->write('Internal Server Error.');
		}
	}

	public function post(Request $request, Response $response, $args): Response
	{
		try {
			$data = json_decode($request->getBody()->getContents());

			try {
				$article = $this->articleRepository->getByIdNotDeleted((int)$args['id']);
			} catch (ArticleNotFoundException $e) {
				return $response->withStatus(404)->write('Not found.');
			}

			$article->setTitle($data->title);
			$article->setContent($data->content);
			$article->setUpdated(new \DateTime());

			$this->entityManager->flush();

			return $response->withJson(['state' => 'ok']);

		} catch (\Exception $e) {
			return $response->withStatus(500)->write('Internal Server Error.');
		}
	}

	public function delete(Request $request, Response $response, $args): Response
	{
		try {
			$this->articleRepository->softDelete((int)$args['id']);
		} catch (ArticleNotFoundException $e) {
			return $response->withStatus(404)->write('Not found.');
		} catch (\Exception $e) {
			return $response->withStatus(500)->write('Internal Server Error.');
		}

		return $response->withJson(['state' => 'ok']);
	}

	public function filter(Request $request, Response $response, $args): Response
	{
		if ($args['field'] === 'title') {

			$articles = $this->articleRepository->findByTitleLike($args['word']);
			$articleResponseCollection = new ArticleResponseCollection($articles);

			return $response->withJson($articleResponseCollection->toArray());
		}

		return $response->withStatus(400)->write('Invalid Argument Exception.');
	}
}
