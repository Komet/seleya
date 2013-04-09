<?php
namespace kvibes\SeleyaBundle\Navbar;

use Knp\Menu\FactoryInterface;
use Mopa\Bundle\BootstrapBundle\Navbar\AbstractNavbarMenuBuilder;
use Symfony\Component\HttpFoundation\Request;

class MenuBuilder extends AbstractNavbarMenuBuilder
{
    private $security;
    private $doctrine;
    
    public function __construct(FactoryInterface $factory, $security, $doctrine)
    {
        $this->security = $security;
        $this->doctrine = $doctrine;
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
    
    public function createUserRightMenu(Request $request)
    {
        $menu = $this->createNavbarMenuItem();
        $menu->setChildrenAttribute('class', 'nav pull-right');
        if ($this->security->isGranted('ROLE_USER')) {
            $username = $this->security->getToken()->getUser()->getUsername();
            $user = $this->doctrine
                         ->getManager()
                         ->getRepository('SeleyaBundle:User')
                         ->getUser($username);
            $dropdown = $this->createDropdownMenuItem($menu, $user->getCommonName(), false, array('caret' => true));
            $dropdown->addChild('Lesezeichen', array('route' => 'bookmarks'));
            $dropdown->addChild('Abmelden', array('route' => 'logout'));
        }
        return $menu;
    }
    
}
