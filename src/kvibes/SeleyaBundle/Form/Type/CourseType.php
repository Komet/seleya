<?php

namespace kvibes\SeleyaBundle\Form\Type;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FacultyType extends AbstractType
{
    private $translator;

    public function __construct($translator)
    {
        $this->translator = $translator;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'kvibes\SeleyaBundle\Entity\Course'
        ));
    }
    
    public function getName()
    {
        return 'course';
    }
}
