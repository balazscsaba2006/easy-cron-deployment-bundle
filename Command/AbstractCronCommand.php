<?php

namespace MadrakIO\EasyCronDeploymentBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

abstract class AbstractCronCommand extends Command
{
    protected function jobArrayToCrontabLine(array $job, $includeNewLine = true): string
    {
        $cronLine = '';

        if (isset($job['disabled']) && true === $job['disabled']) {
            $cronLine = '#';
        }

        $cronLine .= $job['minute'].' '.$job['hour'].' '.$job['day'].' '.$job['month'].' '.$job['day_of_the_week'].' '.$job['task'];

        if (true === $includeNewLine) {
            $cronLine .= PHP_EOL;
        }

        return $cronLine;
    }

    protected function checkJobHasMatchingHostRequirement(array $job): bool
    {
        return 0 === count($job['hosts']) || count($job['hosts']) > 0 && true === in_array(gethostname(), $job['hosts']);
    }

    protected function outputFormattedBlock(OutputInterface $output, array $messages, $type): void
    {
        $output->writeln($this->getHelper('formatter')->formatBlock($messages, $type, true));
    }

    protected function getSystemCrontabList(OutputInterface $output): ?string
    {
        $process = new Process('crontab -l');
        $process->run();

        try {
            $process->mustRun();

            return $process->getOutput();
        } catch (ProcessFailedException $e) {
            $this->outputFormattedBlock($output, ['Error!', 'There was an error while attempting to get the existing crontab list.', $e->getMessage()], 'error');
            exit;
        }
    }

    protected function setSystemCrontab(OutputInterface $output, $newContents): ?string
    {
        $tempCronFileName = '/tmp/madrak_io_easy_cron_deployment.cron.'.time();
        $filesystem = new FileSystem();
        $filesystem->dumpFile($tempCronFileName, $newContents);

        $process = new Process('crontab '.$tempCronFileName);
        try {
            $process->mustRun();

            return $process->getOutput();
        } catch (ProcessFailedException $e) {
            $this->outputFormattedBlock($output, ['Error!', 'There was an error while attempting to overwrite the existing crontab list.', $e->getMessage()], 'error');
            exit;
        }
    }

    protected function interactiveOperationConfirmation(InputInterface $input, OutputInterface $output): bool
    {
        if (false === $input->getOption('non-interactive')) {
            $this->outputFormattedBlock($output, ['Warning!', "You are about to irreversibly overwrite this user's crontab."], 'comment');

            if (false === $this->getHelper('question')->ask($input, $output, new ConfirmationQuestion('Are you sure you want to continue? (y/n)', false))) {
                $this->outputFormattedBlock($output, ['The command has been cancelled.'], 'error');
                exit;
            }
        }

        return true;
    }
}
