<?php
declare(strict_types=1);

namespace Model\Article;

class ArticleRequestBodyValidator
{
    /**
     * @var array
     */
    private static $requiredKeys = ['title', 'content'];

    public static function isValid(array $requestBody): bool
    {
        $countKeysIntersect = count(array_intersect_key(array_flip(self::$requiredKeys), $requestBody));
        $countRequested = count(self::$requiredKeys);

        return $countKeysIntersect === $countRequested;
    }
}
