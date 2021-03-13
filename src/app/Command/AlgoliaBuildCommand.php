<?php
declare(strict_types=1);

namespace Command;

use Model\Search\AlgoliaIndexer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AlgoliaBuildCommand extends Command
{

    /**
     * @var AlgoliaIndexer
     */
    private $indexer;

    public function __construct(AlgoliaIndexer $indexer, string $name = null)
    {
        $this->indexer = $indexer;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('app:algoliaBuild')
            ->setDescription('Build index in Algolia');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $indexedCount = $this->indexer->reindexAll();

        $output->writeln(sprintf('%s items indexed to AlgoliaSearch.', $indexedCount));
    }
}
