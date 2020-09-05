<?php namespace NetzpStaging\Commands;

use Shopware\Commands\ShopwareCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

use NetzpStaging\Component\Task;
use NetzpStaging\Component\FilesTask;
use NetzpStaging\Component\DatabaseTask;

class StagingCreateCommand extends ShopwareCommand
{
    protected function configure()
    {
        $this->setName('staging:create')
             ->addArgument('profile', InputArgument::REQUIRED,
                'Title of the staging environment to create or renew.'
             )
             ->setDescription('Creates or renews a staging environment.')
             ->setHelp('The <info>%command.name%</info> creates or renews a staging environment.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = Shopware()->Container()->get('netzp_staging.helper');
        $title = $input->getArgument('profile');

        set_time_limit(0);

        $liveDir = rtrim(Shopware()->DocPath(), '/');
        $profile = $helper->getProfile(0, $title);

        $helper->log($liveDir, $profile, 'command line', ['create', $title], true);
        $configLive = $helper->readConfigFile($liveDir);

        $output->writeln('<info>processing files...</info>');
        $filesTask = new FilesTask('files', $profile['id'], TASK::TASK_FILES);
        $filesTask->setParams($liveDir, $liveDir . '/' . $profile['dirname'], 
                              $profile, $configLive);
        $filesTask->run();

        $output->writeln('<info>processing database...</info>');
        $databaseTask = new DatabaseTask('database', $profile['id'], TASK::TASK_DATABASE);
        $databaseTask->setAnonymize($profile['dbconfig']['anonymize'] == 1);
        $databaseTask->setParams($liveDir, $liveDir . '/' . $profile['dirname'], 
                                 $profile, $configLive);
        $databaseTask->run();

        $output->writeln('<info>done.</info>');
        $helper->log($liveDir, $profile, 'command line: Fertig');
    }
}