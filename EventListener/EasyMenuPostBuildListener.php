<?php

namespace Xiidea\EasyMenuAclBundle\EventListener;


use Xiidea\EasyMenuAclBundle\Event\EasyMenuEvent;
use Xiidea\EasyMenuAclBundle\Security\AccessFilter;

class EasyMenuPostBuildListener
{

    /**
     * @var AccessFilter
     */
    private $filter;

    public function __construct(AccessFilter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @param EasyMenuEvent $event
     */
    public function onMenuPostBuild(EasyMenuEvent $event)
    {
        $this->filter->apply($event->getMenu());
    }


}