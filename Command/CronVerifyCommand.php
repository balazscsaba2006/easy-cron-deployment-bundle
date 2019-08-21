<?php

namespace MadrakIO\EasyCronDeploymentBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CronVerifyCommand extends AbstractCronCommand
{
    /**
     * @var array
     */
    private $jobs;

    /**
     * @param array $jobs
     */
    public function __construct(array $jobs)
    {
        parent::__construct();

        $this->jobs = $jobs;
    }

    protected function configure()
    {
        $this
            ->setName('madrakio:cron:verify')
            ->setDescription('Verify cron jobs for the user executing this command.')
            ->addOption(
               'strict',
               null,
               InputOption::VALUE_NONE,
               'If set, any extra cron tasks will trigger an error. Otherwise extra tasks are ignored.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $crontabListOutputLines = explode(PHP_EOL, $this->getSystemCrontabList($output));
        if (true === empty($crontabListOutputLines[count($crontabListOutputLines) - 1])) {
            array_pop($crontabListOutputLines);
        }

        $expectedTasks = [];
        foreach ($this->jobs as $job) {
            if (true === $this->checkJobHasMatchingHostRequirement($job)) {
                $expectedTasks[] = $this->jobArrayToCrontabLine($job, false);
            }
        }

        $missingTasks = [];
        foreach ($expectedTasks as $expectedTask) {
            if (false === in_array($expectedTask, $crontabListOutputLines)) {
                $missingTasks[] = $expectedTask;
            }
        }

        if (true === $input->getOption('strict')) {
            $unexpectedTasks = [];
            foreach ($crontabListOutputLines as $crontabLine) {
                if (false === in_array($crontabLine, $expectedTasks)) {
                    $unexpectedTasks[] = $crontabLine;
                }
            }
        }

        if (true === $input->getOption('strict') && count($unexpectedTasks) > 0) {
            $this->outputFormattedBlock($output, ['Error!', 'There was at least one unexpected task in the current crontab:', implode(PHP_EOL, $unexpectedTasks)], 'error');
        }

        if (count($missingTasks) > 0) {
            $this->outputFormattedBlock($output, ['Error!', 'There was at least one task that was missing from the current crontab:', implode(PHP_EOL, $missingTasks)], 'error');
        }

        if ((false === $input->getOption('strict') || true === $input->getOption('strict') && 0 === count($unexpectedTasks)) && 0 === count($missingTasks)) {
            $this->outputFormattedBlock($output, ['Success!', 'Your cron has been successfully verified!'], 'info');
        }
    }
}
