<?php
declare(strict_types=1);

namespace Model\Search;

use Model\Article\Article;

class ArticleMapper
{

    public const FIELD_ID = 'objectID';
    public const FIELD_TITLE = 'title';
    public const FIELD_CONTENT = 'content';

    public static function mapArticle(Article $article): array
    {
        return [
            self::FIELD_ID => $article->getId(),
            self::FIELD_TITLE => $article->getTitle(),
            self::FIELD_CONTENT => $article->getContent(),
        ];
    }
}
