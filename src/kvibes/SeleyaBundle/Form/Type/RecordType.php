<?php

namespace kvibes\SeleyaBundle\Form\Type;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use kvibes\SeleyaBundle\Form\EventListener\MetadataSubscriber;
use kvibes\SeleyaBundle\Form\Type\MetadataType;
use kvibes\SeleyaBundle\Repository\MetadataConfigRepository;
use kvibes\SeleyaBundle\Form\Parameter\MetadataParameter;

class RecordType extends AbstractType
{
    private $metadata;
    
    public function __construct($metadata = null)
    {
        $this->metadata = $metadata;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add('recordDate');
        $builder->add('visible');
        
        foreach ($this->metadata as $meta) {
            $type = $meta->getConfig()->getDefinition()->getId();
            if ($type == 'select') {
                $options = array();
                foreach ($meta->getConfig()->getOptions() as $key => $value) {
                    $options[$value->getId()] = $value->getName();
                }
                $data = ($meta->getValue() !== null) ? $meta->getValue()->getId() : 0;
                $builder->add('metadata_'.$meta->getConfig()->getId(), 'choice', array(
                    'mapped'  => false,
                    'label'   => $meta->getConfig()->getName(),
                    'data'    => $data,
                    'choices' => $options
                ));
            } else {
                $builder->add('metadata_'.$meta->getConfig()->getId(), $type, array(
                    'mapped' => false,
                    'label'  => $meta->getConfig()->getName(),
                    'data'   => $meta->getValue()
                ));
            }
        }
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'kvibes\SeleyaBundle\Entity\Record'
        ));
    }
    
    public function getName()
    {
        return 'record';
    }
}
