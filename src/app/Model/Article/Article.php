<?php
declare(strict_types=1);

namespace Model\Article;

use Doctrine\ORM\Mapping as ORM;
use Model\DateTime\DateTime;
use phpseclib\System\SSH\Agent\Identity;

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
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
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

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getDeleted(): ?\DateTime
    {
        return $this->deleted;
    }

    public function setDeleted(?\DateTime $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTime $updated): void
    {
        $this->updated = $updated;
    }
}
