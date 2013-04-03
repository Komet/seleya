<?php

namespace kvibes\SeleyaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Translation;

class MetadataConfigType extends AbstractType
{
    private $translator;
    
    public function __construct($translator)
    {
        $this->translator = $translator;
    }
    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array(
            'label' => $this->translator->trans('Name')
        ));

        $builder->add('definition', 'entity', array(
            'class'    => 'kvibes\SeleyaBundle\Entity\MetadataConfigDefinition',
            'property' => 'name',
            'label'    => $this->translator->trans('Typ')
        ));
        
        $builder->add('options', 'collection', array(
            'type'         => new MetadataConfigOptionType($this->translator),
            'allow_add'    => true,
            'allow_delete' => true,
            'by_reference' => false
        ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'kvibes\SeleyaBundle\Entity\MetadataConfig',
            'cascade_validation' => true
        ));
    }
    
    public function getName()
    {
        return 'metadataConfig';
    }
}
