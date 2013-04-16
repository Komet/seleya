<?php

namespace kvibes\SeleyaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Translation;

class MetadataConfigOptionType extends AbstractType
{
    private $translator;
    
    public function __construct($translator)
    {
        $this->translator = $translator;
    }
    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            null,
            array('label' => $this->translator->trans('Name'))
        );
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array('data_class' => 'kvibes\SeleyaBundle\Entity\MetadataConfigOption')
        );
    }
    
    public function getName()
    {
        return 'metadataConfigOption';
    }
}
