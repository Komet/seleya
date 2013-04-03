<?php

namespace kvibes\SeleyaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Mopa\Bundle\BootstrapBundle\Navbar\NavbarFormInterface;

class NavbarSearchType extends AbstractType implements NavbarFormInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('render_fieldset', false)
                ->setAttribute('label_render', false)
                ->setAttribute('show_legend', false)
                ->add(
                    'search',
                    'text',
                    array(
                        'widget_control_group' => false,
                        'widget_controls'      => false,
                        'attr' => array(
                            'placeholder' => 'Suche',
                            'class'       => 'input-xlarge search-query'
                        )
                    )
                );
    }

    public function getName()
    {
        return 'navbarSearch';
    }

    public function getRoute()
    {
        return 'index';
    }
}
