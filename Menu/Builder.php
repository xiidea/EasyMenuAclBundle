<?php
namespace Xiidea\EasyMenuAclBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;
use Xiidea\EasyMenuAclBundle\Event\EasyMenuEvent;

class Builder
{
    private $factory;

    /** @var  TraceableEventDispatcher */
    private $eventDispatcher;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createMenu($name)
    {
        $menu = $this->factory->createItem('root');

        $this->eventDispatcher->dispatch(
            "xiidea.easy_menu_build_" . $name,
            new EasyMenuEvent($this->factory, $menu)
        );

        $this->eventDispatcher->dispatch(
            "xiidea.easy_menu_acl_post_build",
            new EasyMenuEvent($this->factory, $menu)
        );

        return $menu;
    }

    /**
     * @param TraceableEventDispatcher $eventDispatcher
     */
    public function setEventDispatcher(TraceableEventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}