<?php

namespace MadrakIO\EasyCronDeploymentBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use MadrakIO\EasyCronDeploymentBundle\Command\AbstractCronCommand;


class CronDeployCommand extends AbstractCronCommand
{
    protected function configure()
    {
        $this
            ->setName('madrakio:cron:deploy')
            ->setDescription('Deploy (and overwrite) cron jobs for the user executing this command.')
            ->addOption(
               'non-interactive',
               null,
               InputOption::VALUE_NONE,
               "If set, you will not be prompted before your user's crontab is overwritten"
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->interactiveOperationConfirmation($input, $output);

        $cronFileContents = '';
        foreach ($this->getContainer()->getParameter('madrak_io_easy_cron_deployment.jobs') AS $job) {
            if ($this->checkJobHasMatchingHostRequirement($job) === true) {
                $cronFileContents .= $this->jobArrayToCrontabLine($job);
            }
        }

        $this->setSystemCrontab($output, $cronFileContents);
        $this->outputFormattedBlock($output, ['Success!', 'Your cron has been successfully deployed!'], 'info');
    }
}
