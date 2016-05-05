<?php

namespace Xiidea\EasyMenuAclBundle\Security;

use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class AccessFilter {

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;
    /**
     * @var RouteAcl
     */
    private $routeAcl;

    public function __construct(AuthorizationChecker $context, RouteAcl $routeAcl)
    {
        $this->authorizationChecker = $context;
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
            if ($this->authorizationChecker->isGranted($role)) {
                return true;
            }
        }

        return false;
    }
}