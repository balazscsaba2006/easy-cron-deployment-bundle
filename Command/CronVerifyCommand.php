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
               "If set, any extra cron tasks will trigger an error. Otherwise extra tasks are ignored."
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $crontabListOutputLines = explode(PHP_EOL, $this->getSystemCrontabList($output));
        if (empty($crontabListOutputLines[count($crontabListOutputLines) - 1]) === true) {
            array_pop($crontabListOutputLines);        
        }
        
        $expectedTasks = [];
        foreach ($this->jobs as $job) {
            if ($this->checkJobHasMatchingHostRequirement($job) === true) {
                $expectedTasks[] = $this->jobArrayToCrontabLine($job, false);                
            }
        }

        $missingTasks = [];
        foreach ($expectedTasks AS $expectedTask) {
            if (in_array($expectedTask, $crontabListOutputLines) === false) {
                $missingTasks[] = $expectedTask;
            }
        }
        
        if ($input->getOption('strict') === true) {
            $unexpectedTasks = [];
            foreach ($crontabListOutputLines AS $crontabLine) {
                if (in_array($crontabLine, $expectedTasks) === false) {
                    $unexpectedTasks[] = $crontabLine;
                } 
            }            
        }
                
        if ($input->getOption('strict') === true && count($unexpectedTasks) > 0) {
            $this->outputFormattedBlock($output, ['Error!', 'There was at least one unexpected task in the current crontab:', implode(PHP_EOL, $unexpectedTasks)], 'error');
        }

        if (count($missingTasks) > 0) {
            $this->outputFormattedBlock($output, ['Error!', 'There was at least one task that was missing from the current crontab:', implode(PHP_EOL, $missingTasks)], 'error');
        }

        if (($input->getOption('strict') === false || $input->getOption('strict') === true && count($unexpectedTasks) === 0) && count($missingTasks) === 0) {
            $this->outputFormattedBlock($output, ['Success!', 'Your cron has been successfully verified!'], 'info');
        }
    }
}
