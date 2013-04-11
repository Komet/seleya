<?php

namespace kvibes\SeleyaBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CourseType extends AbstractType
{
    private $translator;

    public function __construct($translator)
    {
        $this->translator = $translator;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('faculty', 'entity', array(
            'class'    => 'kvibes\SeleyaBundle\Entity\Faculty',
            'property' => 'name',
            'label'    => $this->translator->trans('Fachbereich'),
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('f')->orderBy('f.name', 'ASC');
            },
            'empty_value' => $this->translator->trans('Fachbereich auswählen'),
            'attr'  => array(
                'class' => 'chzn-select'
            )
        ));
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
