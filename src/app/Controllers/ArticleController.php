<?php
declare(strict_types=1);

namespace Controllers;

use Algolia\AlgoliaSearch\Exceptions\BadRequestException;
use Algolia\AlgoliaSearch\SearchClient;
use API\Article\ArticleResponse;
use API\Article\ArticleResponseCollection;
use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use Model\Article\Article;
use Model\Article\ArticleRepository;
use Model\Article\ArticleRequestBodyValidator;
use Model\Exception\ArticleNotFoundException;
use Model\Search\AlgoliaSearchManager;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class ArticleController extends BaseController
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var AlgoliaSearchManager
     */
    private $searchManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EntityManager $entityManager,
        AlgoliaSearchManager $algoliaSearchManager,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->articleRepository = $this->entityManager->getRepository(Article::class);
        $this->searchManager = $algoliaSearchManager;
        $this->logger = $logger;
    }

    public function get(Request $request, Response $response, $args): Response
    {
        try {
            if (isset($args['id'])) {
                try {
                    $article = $this->articleRepository->getByIdNotDeleted((int) $args['id']);
                } catch (ArticleNotFoundException $e) {
                    $response->getBody()->write('Not found');

                    return $response->withStatus(404);
                }

                $response->getBody()->write(
                    (new ArticleResponse($article))->toJson()
                );

                return $response->withHeader('Content-Type', 'application/json');
            }

            $allArticles = $this->articleRepository->findAllOrderedByLastUpdate();

            $articleResponse = new ArticleResponseCollection($allArticles);
            $response->getBody()->write($articleResponse->toJson());

            return $this->returnJson($response);
        } catch (\Exception $e) {
            $response->getBody()->write('Internal Server Error');
            $this->logger->error($e->getMessage(), ['error' => $e]);

            return $response->withStatus(500);
        }
    }

    public function put(Request $request, Response $response, $args): Response
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);

            if (ArticleRequestBodyValidator::isValid($data)) {
                $article = new Article($data['title'], $data['content']);
                $this->entityManager->persist($article);
                $this->entityManager->flush();
            } else {
                $response->getBody()->write('Invalid request body');

                return $response->withStatus(422);
            }

            $body = ['state' => 'ok', 'article_id' => $article->getId()];
            $response->getBody()->write(json_encode($body));

            return $this->returnJson($response);
        } catch (\Throwable $e) {
            $response->getBody()->write('Internal Server Error');
            $this->logger->error($e->getMessage(), ['error' => $e]);

            return $response->withStatus(500);
        }
    }

    public function post(Request $request, Response $response, $args): Response
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);

            try {
                $article = $this->articleRepository->getByIdNotDeleted((int) $args['id']);
            } catch (ArticleNotFoundException $e) {
                $response->getBody()->write('Not found.');

                return $response->withStatus(404);
            }

            if (ArticleRequestBodyValidator::isValid($data)) {
                $article->setTitle($data['title']);
                $article->setContent($data['content']);
                $article->setUpdated(new DateTime());
                $this->entityManager->flush();

                try {
                    $this->searchManager->saveArticle($article);
                } catch (BadRequestException $exception) {
                    $this->logger->error($exception->getMessage(), ['error' => $exception]);
                }

                $body = ['state' => 'ok'];
                $response->getBody()->write(json_encode($body));

                return $this->returnJson($response);
            }

            $response->getBody()->write('Invalid request body');

            return $response->withStatus(422);
        } catch (Exception $e) {
            $response->getBody()->write('Internal Server Error.');
            $this->logger->error($e->getMessage(), ['error' => $e]);

            return $response->withStatus(500);
        }
    }

    public function delete(Request $request, Response $response, $args): Response
    {
        try {
            $this->articleRepository->softDelete((int) $args['id']);
            $body = ['state' => 'ok'];
            $response->getBody()->write(json_encode($body));

            try {
                $this->searchManager->deleteArticle((int) $args['id']);
            } catch (BadRequestException $exception) {
                $this->logger->error($exception->getMessage(), ['error' => $exception]);
            }

            return $this->returnJson($response);
        } catch (ArticleNotFoundException $e) {
            $response->getBody()->write('Not found.');

            return $response->withStatus(404);
        } catch (Exception $e) {
            $response->getBody()->write('Internal Server Error.');

            return $response->withStatus(500);
        }
    }

    public function filter(Request $request, Response $response, $args): Response
    {

        if ($args['field'] === 'title') {
            $articles = $this->articleRepository->findByTitleLike($args['word']);
            $articleResponseCollection = new ArticleResponseCollection($articles);

            $response->getBody()->write($articleResponseCollection->toJson());

            return $this->returnJson($response);
        }

        $response->getBody()->write('Invalid Argument Exception.');

        return $response->withStatus(422);
    }

    public function search(Request $request, Response $response, $args): Response
    {
        $index = $this->searchClient->initIndex('articles');

        $res = $index->search($args['query']);

        $response->getBody()->write(\GuzzleHttp\json_encode($res));

        return $this->returnJson($response);
    }
}
