<?php

namespace Controllers;

use Doctrine\ORM\EntityManager;
use Model\Article\Article;
use Model\Article\ArticleRepository;
use Nette\Database\Connection;
use Nette\Database\Context;
use Nette\Utils\DateTime;
use Slim\Http\Request;
use Slim\Http\Response;

class ArticleController
{

	/**
	 * @var Connection
	 */
	private $database;

	/**
	 * @var Context
	 */
	private $context;

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var ArticleRepository
	 */
	private $articleRepository;

	public function __construct(Connection $database, Context $context, EntityManager $entityManager)
	{
		$this->database = $database;
		$this->context = $context;
		$this->em = $entityManager;
		$this->articleRepository = $this->em->getRepository(Article::class);
	}

	public function get(Request $request, Response $response, $args): Response
	{

		if (isset($args['id'])) {
			$data = $this->database->query('SELECT * FROM article WHERE isnull(deleted) AND article_id=?', $args['id'])->fetch();

			return $response->withJson($data);
		}
		$allArticles = $this->articleRepository->findAllOrderedByLastUpdate();

		return $response->withJson($allArticles);
	}

	public function put(Request $request, Response $response, $args)
	{
		$data = json_decode($request->getBody()->getContents());
		$this->database->query('INSERT INTO article', ['title' => $data->title, 'content' => '', 'last_update' => new DateTime]);
		$articleId = $this->database->getInsertId();
		return $response->withJson(['state' => 'ok', 'article_id' => $articleId]);
	}

	public function post(Request $request, Response $response, $args)
	{
		$data = json_decode($request->getBody()->getContents());
		$this->database->query('UPDATE article SET ? WHERE article_id = ?',
			['title' => $data->title, 'content' => $data->content, 'last_update' => new DateTime()],
			$args['id']
		);
		return $response->withJson(['state' => 'ok']);
	}

	public function delete(Request $request, Response $response, $args)
	{
		$this->database->query('UPDATE article SET deleted=NOW() WHERE article_id=?', $args['id']);
		return $response->withJson(['state' => 'ok']);
	}

	public function filter(Request $request, Response $response, $args)
	{
		if ($args['field'] === 'title') {
			$articles = $this->context->table('article');
			$articles->where('title LIKE ?', '%' . $args['word'] . "%");
			$articles->where('deleted IS NULL');

			$data = [];
			foreach ($articles->fetchAll() as $one) {
				$data[] = ['article_id' => $one->article_id, 'title' => $one->title];
			}

			return $response->withJson($data);
		}
	}
}
