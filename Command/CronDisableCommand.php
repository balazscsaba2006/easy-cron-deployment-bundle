<?php

namespace MadrakIO\EasyCronDeploymentBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CronDisableCommand extends AbstractCronCommand
{
    protected function configure()
    {
        $this
            ->setName('madrakio:cron:disable')
            ->setDescription('Automatically comment all tasks in the crontab.')
            ->addOption(
               'non-interactive',
               null,
               InputOption::VALUE_NONE,
               "If set, you will not be prompted before your user's crontab is commented"
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->interactiveOperationConfirmation($input, $output);

        $crontabListOutputLines = explode(PHP_EOL, $this->getSystemCrontabList($output));
        if (true === empty($crontabListOutputLines[count($crontabListOutputLines) - 1])) {
            array_pop($crontabListOutputLines);
        }

        $newCrontabFileContents = '';
        foreach ($crontabListOutputLines as $crontabLine) {
            $newCrontabFileContents .= '#'.$crontabLine.PHP_EOL;
        }

        $this->setSystemCrontab($output, $newCrontabFileContents);
        $this->outputFormattedBlock($output, ['Success!', 'Your cron has been successfully disabled!'], 'info');
    }
}
