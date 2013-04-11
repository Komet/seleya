<?php
namespace kvibes\SeleyaBundle\Menu;

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

        $records = $this->createDropdownMenuItem($menu, 'Aufzeichnungen', false, array('caret' => true));
        $records->addChild('Neue Aufzeichnungen', array('route' => 'admin_record'));
        $records->addChild('Sichtbare Aufzeichnungen', array('route' => 'admin_record_visible'));
        
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $this->addDivider($records);
            $records->addChild('Aufzeichnungen importieren', array('route' => 'admin_record_import'));
            $records->addChild('Aufzeichnung hinzufügen', array('route' => 'admin_record_new'));

            $faculty = $this->createDropdownMenuItem($menu, 'Fachbereiche', false, array('caret' => true));
            $faculty->addChild('Übersicht', array('route' => 'admin_faculty'));
            $faculty->addChild('Hinzufügen', array('route' => 'admin_faculty_new'));

            $course = $this->createDropdownMenuItem($menu, 'Veranstaltungen', false, array('caret' => true));
            $course->addChild('Übersicht', array('route' => 'admin_course'));
            $course->addChild('Hinzufügen', array('route' => 'admin_course_new'));
            
            $metadata = $this->createDropdownMenuItem($menu, 'Metadaten', false, array('caret' => true));
            $metadata->addChild('Übersicht', array('route' => 'admin_metadataConfig'));
            $metadata->addChild('Hinzufügen', array('route' => 'admin_metadataConfig_new'));
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

    public function createMenu(Request $request)
    {
        $menu = $this->createNavbarMenuItem();
        $menu->addChild('Fachbereiche', array('route' => 'faculties'));
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
            $this->addDivider($dropdown);
            $dropdown->addChild('Abmelden', array('route' => 'logout'));
        }
        return $menu;
    }
    
}
