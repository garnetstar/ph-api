<?php
declare(strict_types=1);

namespace Model\Article;

use PHPUnit\Framework\TestCase;

class ArticleRequestBodyValidatorTest extends TestCase
{

    /**
     * @param array $parameters
     * @param $expected
     * @dataProvider dataProvider
     */
    public function testIsValid(array $parameters, $expected): void
    {
        $result = ArticleRequestBodyValidator::isValid($parameters);
        $this->assertSame($expected, $result);
    }

    public function dataProvider(): array
    {
        return [
            'exact' => [
                'parameters' => [
                    'title' => '',
                    'content' => '',
                ],
                'expected' => true,
            ],
            'missing' => [
                'parameters' => [
                    'content' => '',
                ],
                'expected' => false,
            ],
            'empty' => [
                'parameters' => [
                ],
                'expected' => false,
            ],
            'more' => [
                'parameters' => [
                    'yyy' => '',
                    'title' => '',
                    'content' => '',
                    'xxx' => '',
                ],
                'expected' => true,
            ],
        ];
    }
}
