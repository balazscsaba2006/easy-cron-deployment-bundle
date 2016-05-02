<?php

namespace MadrakIO\EasyCronDeploymentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('madrak_io_easy_cron_deployment');

        $rootNode
            ->children()
                ->arrayNode('jobs')
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->isRequired()
                        ->children()
                            ->arrayNode('hosts')
                                ->prototype('scalar')->end()
                            ->end()
                            ->booleanNode('disabled')
                                ->info("Should the job be commented so that it doesn't run?")
                                ->defaultValue(false)
                            ->end()
                            ->scalarNode('minute')
                                ->info('The minute(s) at which the job should be triggered. 0-59. Defaults to *.')            
                                ->defaultValue('*')            
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('hour')
                                ->info('The hour(s) at which the job should be triggered. 0-23. Defaults to *.')
                                ->defaultValue('*')
                                ->cannotBeEmpty()            
                            ->end()
                            ->scalarNode('day')
                                ->info('The day(s) of the month at which the job should be triggered. 0-31. Defaults to *.')            
                                ->defaultValue('*')            
                                ->cannotBeEmpty()            
                            ->end()
                            ->scalarNode('month')
                                ->info('The month(s) at which the job should be triggered. 0-12. Defaults to *.')            
                                ->defaultValue('*')            
                                ->cannotBeEmpty()            
                            ->end()
                            ->scalarNode('day_of_the_week')
                                ->info('The day(s) of the week at which the job should be triggered. 0 (Sunday) - 6 (Saturday). Defaults to *.')            
                                ->defaultValue('*')            
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('task')
                                ->info('The command that should be triggered by the cron.')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
                    

        return $treeBuilder;
    }
}
