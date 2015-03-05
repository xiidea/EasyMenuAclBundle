<?php

namespace Xiidea\EasyMenuAclBundle\Security;

use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\SecurityContext;

class AccessFilter {

    /**
     * @var SecurityContext
     */
    private $context;
    /**
     * @var RouteAcl
     */
    private $routeAcl;

    public function __construct(SecurityContext $context, RouteAcl $routeAcl)
    {
        $this->context = $context;
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
        $roles = $this->routeAcl->getRoles($uri);
        foreach ($roles as $role) {
            if ($this->context->isGranted($role)) {
                return true;
            }
        }

        return false;
    }
}