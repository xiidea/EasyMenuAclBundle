<?php

namespace Xiidea\EasyMenuAclBundle\Security;

use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\AccessMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class RouteAcl
{
    /** @var SecurityContextInterface */
    private $context;

    /** @var AccessDecisionManagerInterface */
    private $accessDecisionManager;

    /** @var AccessMap */
    private $map;

    /** @var Router */
    private $router;

    public function __construct(
        SecurityContextInterface $context,
        AccessDecisionManagerInterface $accessDecisionManager,
        AccessMap $map,
        Router $router
    ) {
        $this->context = $context;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->map = $map;
        $this->router = $router;
    }

    public function isAccesible($path)
    {
        if (null === $token = $this->context->getToken()) {
            return true;
        }

        $baseUrl = $this->router->getContext()->getBaseUrl();
        $path = substr($path, strlen($baseUrl));

        $request = Request::create($path, 'GET');

        list($roles, $channel) = $this->map->getPatterns($request);

        if (null === $roles) {
            return true;
        }

        if (!$token->isAuthenticated()) {
            return false;
        }

        return $this->accessDecisionManager->decide($token, $roles, $request);
    }
}