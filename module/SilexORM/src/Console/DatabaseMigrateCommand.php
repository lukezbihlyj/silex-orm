<?php

namespace LukeZbihlyj\SilexORM\Console;

use LukeZbihlyj\SilexPlus\Console\ConsoleCommand;
use LukeZbihlyj\SilexORM\Query\Resolver;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package LukeZbihlyj\SilexORM\Console\DatabaseMigrateCommand
 */
class DatabaseMigrateCommand extends ConsoleCommand
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('database:migrate')
            ->setDescription('Run automated migration for all known entities.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getSilexApp();

        foreach ($app['database.entities'] as $entity) {
            $output->writeln('<comment>Found entity ' . $entity . ', running migration...</comment>');

            $mapper = $app->getSpot()->mapper($entity);
            $resolver = new Resolver($mapper);

            $resolver->migrate();
        }

        $output->writeln('<info>Finished migration!</info>');
    }
}
