<?php
declare(strict_types=1);

namespace Command;

use Doctrine\ORM\EntityManager;
use Model\Article\Article;
use Nette\Database\Connection;
use Nette\Utils\DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var Connection
	 */
	private $netteDatabase;

	/**
	 * MigrateCommand constructor.
	 * @param EntityManager $entityManager
	 * @param Connection $netteDatabase
	 */
	public function __construct(EntityManager $entityManager, Connection $netteDatabase, string $name = null)
	{
		$this->entityManager = $entityManager;
		$this->netteDatabase = $netteDatabase;

		parent::__construct($name);
	}

	protected function configure()
	{
		$this->setName('app:migrate')
			->setDescription('Migrate data from nette database to Doctrine');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->migrateArticle();
	}

	private function migrateArticle(): void
	{
		$data = $this->netteDatabase->query('SELECT * FROM article')->fetchAll();

		foreach ($data as $one) {

			$title = $one['title'];
			$content = $one['content'];
			$updated = $this->convertFromNetteDateTime($one['last_update']);
			$deleted = $this->convertFromNetteDateTime($one['deleted']);

			$article = new Article($title, $content);
			$article->setDeleted($deleted);
			$article->setUpdated($updated);
			$this->entityManager->persist($article);
		}

		$this->entityManager->flush();
	}

	private function convertFromNetteDateTime(?DateTime $dateTime): ?\DateTime
	{
		if ($dateTime === null) {
			return null;
		}

		$nativeDateTime = new \DateTime();
		$nativeDateTime->setTimestamp($dateTime->getTimestamp());

		return $nativeDateTime;
	}
}
