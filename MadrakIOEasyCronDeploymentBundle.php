<?php

namespace MadrakIO\EasyCronDeploymentBundle;

use MadrakIO\EasyCronDeploymentBundle\DependencyInjection\CompilerPass\JobsAwareCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MadrakIOEasyCronDeploymentBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new JobsAwareCompilerPass());
    }
}
