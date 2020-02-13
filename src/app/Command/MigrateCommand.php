<?php
declare(strict_types=1);

namespace Command;

use Doctrine\ORM\EntityManager;
use Model\Article\Article;
use Model\User\User;
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
     *
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
        $this->migrateUsers();
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

    private function migrateUsers(): void
    {
        $data = $this->netteDatabase->query('SELECT * FROM user')->fetchAll();

        foreach ($data as $one) {
            $login = (string) $one['login'];
            $token = (string) $one['token'];

            $user = new User($login, $token);
            $this->entityManager->persist($user);
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
