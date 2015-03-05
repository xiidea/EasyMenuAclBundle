Easy Menu Acl Bundle
====================

A Symfony2 Bundle To Power up KnpMenuBundle. This bundle can be user to register menu with simple configuration. or
can be used with zero configuration to filter menus as per security access level.

Install
-------
1. Add EasyMenuAclBundle in your composer.json
2. Enable the Bundle
3. Configure config.yml(Optional)

### 1. Add EasyAuditBundle in your composer.json

Add EasyAuditBundle in your composer.json:

```js
{
    "require": {
        "xiidea/easy-menu-acl-bundle": "1.0.*@dev"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update xiidea/easy-menu-acl-bundle
```

Composer will install the bundle to your project's `vendor/xiidea` directory.

### 2. Enable the Bundle

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Xiidea\EasyMenuAclBundle\XiideaEasyMenuAclBundle(),
    );
}
```

### 3. Configure config.yml

``` yaml
# app/config/config.yml
xiidea_easy_menu_acl:
    # builders : [main, sidebar]

```

Cookbook
--------
You can use this bundle 3(three) way.

#### 1 Register menu with event listener.
First define builder configuration with as many menu as you need.

``` yaml
# app/config/config.yml
xiidea_easy_menu_acl:
    builders : [main, sidebar]

```

Then define event listener services to listen on `xiidea.easy_menu_build_{THE_MENU_NAME}`. For the example configuration
there would be two events `xiidea.easy_menu_build_main` and `xiidea.easy_menu_build_sidebar`

``` yaml
# service.yml
    menu_build_listener:
        class: AppBundle\EventListener\MenuListener
        arguments: [@event_dispatcher]
        tags:
            - { name: kernel.event_listener, event: xiidea.easy_menu_build_main, method: buildMainMenu}
            - { name: kernel.event_listener, event: xiidea.easy_menu_build_sidebar, method: buildSideBarMenu}

```

Define the menuListener class

```php
<?php
class MainMenuListener
{

    /**
     * @var TraceableEventDispatcher
     */
    private $dispatcher;

    public function __construct(TraceableEventDispatcher $dispatcher){

        $this->dispatcher = $dispatcher;
    }

    /**
     * @param EasyMenuEvent $event
     */
    public function buildMainMenu(EasyMenuEvent $event)
    {
        $menu = $event->getMenu();
        $factory = $event->getFactory();

        $menu->addChild('Home', array('uri' => '/'));
        $menu->addChild('Reports', array('route' => 'report_route'));

        //..

    }

    /**
     * @param EasyMenuEvent $event
     */
    public function buildSideBarMenu(EasyMenuEvent $event)
    {
        $menu = $event->getMenu();
        $factory = $event->getFactory();

        $menu->addChild('Home Page', array('uri' => '/'));
        $menu->addChild('Reports', array('route' => 'report_route'));

        //..

    }
}

```



#### 2. Zero configuration dispatching event:
You can use the bundle without configuration. You then need to dispatch an event `xiidea.easy_menu_acl_post_build` after you build your menu.
Like :

```php
<?php
//Menu builder

use Xiidea\EasyMenuAclBundle\Event\EasyMenuEvent;

class MenuBuilder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Home', array('uri' => '/'));

        $menu->addChild('Reports', array('route' => 'report_route'));

        //.....

        $this->container->get('event_dispatcher')->dispatch(
            "xiidea.easy_menu_acl_post_build",
            new EasyMenuEvent($factory, $menu)
        );

        return $menu;
    }

```

#### 3. Zero configuration using access filter service:

You can use the `xiidea.easy_menu_acl.access_filter` and apply filter on menu object.

```php
<?php
//Menu builder

class MenuBuilder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Home', array('uri' => '/'));

        $menu->addChild('Reports', array('route' => 'report_route'));

        //.....

        return $this->container->get('xiidea.easy_menu_acl.access_filter')->apply($menu);
    }

```
