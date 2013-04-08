<?php
namespace kvibes\SeleyaBundle\Navbar;

use Knp\Menu\FactoryInterface;
use Mopa\Bundle\BootstrapBundle\Navbar\AbstractNavbarMenuBuilder;
use Symfony\Component\HttpFoundation\Request;

class MenuBuilder extends AbstractNavbarMenuBuilder
{
    private $security;
    
    public function __construct(FactoryInterface $factory, $security)
    {
        $this->security = $security;
        parent::__construct($factory);
    }
    
    public function createAdminMenu(Request $request)
    {
        $menu = $this->createNavbarMenuItem();

        $menu->addChild('Aufzeichnungen', array('route' => 'admin_record'));

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $dropdown = $this->createDropdownMenuItem($menu, 'Metadaten', false, array('caret' => true));
            $dropdown->addChild('Übersicht', array('route' => 'admin_metadataConfig'));
            $dropdown->addChild('Hinzufügen', array('route' => 'admin_metadataConfig_new'));
        }        
        
        return $menu;
    }
    
    public function createAdminRightMenu(Request $request)
    {
        $menu = $this->createNavbarMenuItem();
        $menu->addChild('Logout', array('route' => 'admin_logout'));
        $menu->setChildrenAttribute('class', 'nav pull-right');
        return $menu;
    }

    public function createAdminRecordMenu(Request $request)
    {
        $menu = $this->createSubnavbarMenuItem();
        $menu->setChildrenAttribute('class', 'nav nav-pills nav-stacked');
        $menu->addChild('Neue Aufzeichnungen', array('route' => 'admin_record'));
        $menu->addChild('Alle Aufzeichnungen', array('route' => 'admin_record_visible'));
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $menu->addChild('Aufzeichnungen importieren', array('route' => 'admin_record_import'));
            $menu->addChild('Aufzeichnung hinzufügen', array('route' => 'admin_record_new'));
        }
        return $menu;
    }

    public function createMenu(Request $request)
    {
        $menu = $this->createNavbarMenuItem();
        $menu->addChild('Fachbereiche', array('route' => 'course'));
        $menu->addChild('Veranstaltungen', array('route' => 'series'));
        return $menu;
    }
    
    public function createUserSideMenu(Request $request)
    {
        $menu = $this->createSubnavbarMenuItem();
        $menu->setChildrenAttribute('class', 'nav nav-pills nav-stacked');
        $menu->addChild('Lesezeichen', array('route' => 'bookmarks'));
        $menu->addChild('Abmelden', array('route' => 'logout'));
        return $menu;
    }
}
