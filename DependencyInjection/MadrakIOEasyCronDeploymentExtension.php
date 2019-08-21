<?php

namespace MadrakIO\EasyCronDeploymentBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class MadrakIOEasyCronDeploymentExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');

        $jobAwareCommandIds = $container->findTaggedServiceIds('easy_cron_deployment_jobs_aware_command');
        if (!$jobAwareCommandIds) {
            return;
        }

        foreach (array_keys($jobAwareCommandIds) as $commandId) {
            $definition = $container->getDefinition($commandId);
            $definition->setArgument('$jobs', $config['jobs']);
        }
    }
}
