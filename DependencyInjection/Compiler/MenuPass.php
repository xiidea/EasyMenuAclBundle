<?php
namespace Xiidea\EasyMenuAclBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class MenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if(!$container->hasParameter('xiidea.easy_menu.builders')) {
            return;
        }

        $builders = $container->getParameter('xiidea.easy_menu.builders');

        if(empty($builders)) {
            return;
        }

        if(is_string($builders)) {
            $builders = array($builders);
        }

        $this->addMenusToMenuProvider($container, $builders);
    }

    /**
     * @param ContainerBuilder $container
     * @param $builders
     */
    private function addMenusToMenuProvider(ContainerBuilder $container, $builders)
    {
        $definition = $container->getDefinition('knp_menu.menu_provider.container_aware');
        $menus = $definition->getArgument(1);

        foreach ($builders as $menu) {
            $menuId = "xiidea.easy_menu_item_" . $menu;
            $this->registerMenuService($container, $menuId, $menu);
            $menus[$menu] = $menuId;
        }

        if (!empty($menus)) {
            $definition->replaceArgument(1, $menus);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param $menuId
     * @param $menu
     */
    private function registerMenuService(ContainerBuilder $container, $menuId, $menu)
    {
        $container
            ->register($menuId, 'Knp\Menu\MenuItem')
            ->setFactoryService('xiidea.easy_menu_acl.menu_builder')
            ->setFactoryMethod('createMenu')
            ->setArguments(array($menu))
            ->addTag('knp_menu.menu', array('alias' => $menu));
    }
}
