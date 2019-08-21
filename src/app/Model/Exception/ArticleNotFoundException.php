<?php
declare(strict_types=1);

namespace Model\Exception;

class ArticleNotFoundException extends \Exception
{
	public static function byIdNotDeleted(int $id): self
	{
		return new static(sprintf('Article with id = %d and not deleted not found.', $id));
	}
}
