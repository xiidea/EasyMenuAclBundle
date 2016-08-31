<?php

namespace Xiidea\EasyMenuAclBundle\Security;

use Knp\Menu\ItemInterface;

class AccessFilter {

    /**
     * @var RouteAcl
     */
    private $routeAcl;

    public function __construct(RouteAcl $routeAcl)
    {
        $this->routeAcl = $routeAcl;
    }

    public function apply($getMenu)
    {
        $this->processMenuItem($getMenu);
    }

    private function processMenuItem(ItemInterface $menu){

        $uri = $menu->getUri();

        if(!empty($uri)) {
            if(false === $this->hsaAccess($uri)) {
                $menu->getParent()->removeChild($menu);
                return;
            }
        }

        if($menu->hasChildren()) {
            foreach($menu->getChildren() as $item) {
                $this->processMenuItem($item);
            }
        }

        if(empty($uri) && $menu->getName() !='root' && !$menu->hasChildren()) {
            $menu->getParent()->removeChild($menu);
        }
    }

    /**
     * @param string $uri
     * @return bool
     */
    private function hsaAccess($uri = "")
    {
        return $this->routeAcl->isAccesible($uri);
    }
}