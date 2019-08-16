<?php
declare(strict_types=1);

namespace Model\Article;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="Model\Article\ArticleRepository")
 */
class Article
{

	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	private $id;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $title;

	/**
	 * @var string
	 * @ORM\Column(type="text")
	 */
	private $content;

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetime")
	 */
	private $updated;

	/**
	 * @var \DateTime|null
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $deleted;

	public function __construct(string $title, string $content)
	{
		$this->title = $title;
		$this->content = $content;
		$this->updated = new \DateTime();
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getContent(): string
	{
		return $this->content;
	}
}
