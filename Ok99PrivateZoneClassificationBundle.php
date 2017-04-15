<?php

namespace Ok99\PrivateZoneCore\ClassificationBundle;

use Ok99\PrivateZoneCore\ClassificationBundle\DependencyInjection\Compiler\ContextCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Ok99PrivateZoneClassificationBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ContextCompilerPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataClassificationBundle';
    }
}