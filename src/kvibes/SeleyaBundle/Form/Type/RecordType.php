<?php

namespace kvibes\SeleyaBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
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
    private $translator;
    private $metadata;

    public function __construct($translator, $metadata = null)
    {
        $this->translator = $translator;
        $this->metadata = $metadata;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'title',
            null,
            array('label' => $this->translator->trans('Titel'))
        );
        $builder->add(
            'recordDate',
            null,
            array('label' => $this->translator->trans('Datum der Aufzeichnung'))
        );
        $builder->add(
            'visible',
            null,
            array(
                'label'    => $this->translator->trans('Sichtbar'),
                'required' => false
            )
        );
        $builder->add(
            'course',
            'entity',
            array(
                'class'    => 'kvibes\SeleyaBundle\Entity\Course',
                'property' => 'name',
                'label'    => $this->translator->trans('Veranstaltung'),
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')->orderBy('c.name', 'ASC');
                },
                'empty_value' => 'Veranstaltung auswählen',
                'attr'  => array(
                    'class' => 'chzn-select',
                )
            )
        );

        $builder->add(
            'lecturers',
            'entity',
            array(
                'class'    => 'kvibes\SeleyaBundle\Entity\User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                              ->where('u.admin=true')
                              ->orderBy('u.commonName', 'ASC');
                },
                'label'    => $this->translator->trans('Dozenten'),
                'required' => false,
                'multiple' => true,
                'attr'  => array(
                    'class' => 'chzn-select',
                    'data-placeholder' => $this->translator->trans('Benutzer auswählen')
                )
            )
        );

        $builder->add(
            'users',
            'entity',
            array(
                'class'    => 'kvibes\SeleyaBundle\Entity\User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                              ->where('u.admin=true')
                              ->orderBy('u.commonName', 'ASC');
                },
                'label'    => $this->translator->trans('Weitere Benutzer mit Schreibrechten'),
                'required' => false,
                'multiple' => true,
                'attr'  => array(
                    'class' => 'chzn-select',
                    'data-placeholder' => $this->translator->trans('Benutzer auswählen')
                )
            )
        );

        foreach ($this->metadata as $meta) {
            $type = $meta->getConfig()->getDefinition()->getId();
            if ($type == 'select') {
                $options = array();
                foreach ($meta->getConfig()->getOptions() as $key => $value) {
                    $options[$value->getId()] = $value->getName();
                }
                $data = ($meta->getValue() !== null) ? $meta->getValue()->getId() : 0;
                $builder->add(
                    'metadata_'.$meta->getConfig()->getId(),
                    'choice',
                    array(
                        'mapped'   => false,
                        'label'    => $meta->getConfig()->getName(),
                        'data'     => $data,
                        'choices'  => $options,
                        'required' => false
                    )
                );
            } else {
                $builder->add(
                    'metadata_'.$meta->getConfig()->getId(),
                    $type,
                    array(
                        'mapped'   => false,
                        'label'    => $meta->getConfig()->getName(),
                        'data'     => $meta->getValue(),
                        'required' => false
                    )
                );
            }
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array('data_class' => 'kvibes\SeleyaBundle\Entity\Record')
        );
    }

    public function getName()
    {
        return 'record';
    }
}
