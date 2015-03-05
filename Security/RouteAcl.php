<?php

namespace Xiidea\EasyMenuAclBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\AccessMap;

class RouteAcl
{
    /**
     * @var AccessMap
     */
    private $accessMap;

    public function __construct(AccessMap $accessMap)
    {
        $this->accessMap = $accessMap;
    }

    public function getRoles($path)
    {
        $request = Request::create($path, 'GET');
        list($roles, $channel) = $this->accessMap->getPatterns($request);

        return $roles;
    }
}