<?php

namespace kvibes\SeleyaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MetadataConfigDefinitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'kvibes\SeleyaBundle\Entity\MetadataConfigDefinition'
        ));
    }
    
    public function getName()
    {
        return 'metadataConfigDefinition';
    }
}
