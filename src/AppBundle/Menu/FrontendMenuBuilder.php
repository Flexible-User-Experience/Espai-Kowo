<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FrontendMenuBuilder
 *
 * @category Menu
 * @package  AppBundle\Menu
 * @author   David Romaní <david@flux.cat>
 */
class FrontendMenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var AuthorizationChecker
     */
    private $ac;

    /**
     * @var TranslatorInterface
     */
    private $ts;

    /**
     * @param FactoryInterface     $factory
     * @param AuthorizationChecker $ac
     * @param TranslatorInterface  $ts
     */
    public function __construct(FactoryInterface $factory, AuthorizationChecker $ac, TranslatorInterface $ts)
    {
        $this->factory = $factory;
        $this->ac = $ac;
        $this->ts = $ts;
    }

    /**
     * @param RequestStack $requestStack
     *
     * @return ItemInterface
     */
    public function createTopMenu(RequestStack $requestStack)
    {
        $route = $requestStack->getCurrentRequest()->get('_route');
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');
        if ($this->ac->isGranted('ROLE_CMS')) {
            $menu->addChild(
                'admin',
                array(
                    'label' => '[CMS]',
                    'route' => 'sonata_admin_dashboard',
                )
            );
        }
        $menu->addChild(
            'front_blog',
            array(
                'label' => 'BLOG',
                'route' => 'front_blog',
                'current' => $route == 'front_blog' || $route == 'front_detail',
            )
        );
        $menu->addChild(
            'front_coworkers_list',
            array(
                'label'   => 'COWORKERS',
                'route'   => 'front_coworkers_list',
                'current' => $route == 'front_coworkers_list' || $route == 'front_coworkers_detail',
            )
        );
        $menu->addChild(
            'front_events_list',
            array(
                'label'   => 'ACTIVITATS',
                'route'   => 'front_events_list',
                'current' => $route == 'front_events_list' || $route == 'front_events_detail',
            )
        );

        return $menu;
    }
}
