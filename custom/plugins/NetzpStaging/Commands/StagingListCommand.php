<?php namespace NetzpStaging\Commands;

use Shopware\Commands\ShopwareCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class StagingListCommand extends ShopwareCommand
{
    protected function configure()
    {
        $this->setName('staging:list')
             ->setDescription('Lists all staging environments.')
             ->setHelp('The <info>%command.name%</info> lists all staging environments.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = Shopware()->Container()->get('netzp_staging.helper');

        $profiles = $helper->getProfiles();
        $data = array_map(function($record) {
            return [
                $record['title'], 
                $record['dirname'],
                $record['createdfiles'],
                $record['createddb'],
                $record['runfromcron'],
            ];
        }, $profiles);

        $table = new Table($output);
        $table->setHeaders(array('Title', 'Directory', 'Created files', 'Created database', 'Execute via cron'))
              ->setRows($data);
        $table->render();
    }
}