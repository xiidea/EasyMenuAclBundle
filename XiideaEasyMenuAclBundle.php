<?php

namespace Xiidea\EasyMenuAclBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Xiidea\EasyMenuAclBundle\DependencyInjection\Compiler\MenuPass;

class XiideaEasyMenuAclBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new MenuPass());
    }
}
