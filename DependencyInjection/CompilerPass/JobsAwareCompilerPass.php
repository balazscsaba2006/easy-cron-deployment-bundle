<?php

namespace MadrakIO\EasyCronDeploymentBundle\DependencyInjection\CompilerPass;

use MadrakIO\EasyCronDeploymentBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class JobsAwareCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        $commandIds = $container->findTaggedServiceIds('easy_cron_deployment_jobs_aware_command');
        if (!$commandIds) {
            return;
        }

        $config = $this->getConfiguration($container);

        foreach (array_keys($commandIds) as $commandId) {
            $definition = $container->getDefinition($commandId);
            $definition->setArgument('$jobs', $config['jobs']);
        }
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function getConfiguration(ContainerBuilder $container): array
    {
        $configs = $container->getExtensionConfig('madrak_io_easy_cron_deployment');
        $configuration = new Configuration();

        return $this->processConfiguration($configuration, $configs);
    }

    /**
     * @param ConfigurationInterface $configuration
     * @param array $configs
     *
     * @return array
     */
    private function processConfiguration(ConfigurationInterface $configuration, array $configs): array
    {
        $processor = new Processor();

        return $processor->processConfiguration($configuration, $configs);
    }
}
