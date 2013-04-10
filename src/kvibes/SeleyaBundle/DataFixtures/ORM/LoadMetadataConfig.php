<?php

namespace kvibes\SeleyaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use kvibes\SeleyaBundle\Entity\MetadataConfig;
use kvibes\SeleyaBundle\Entity\MetadataConfigDefinition;
use kvibes\SeleyaBundle\Entity\MetadataConfigOption;

class LoadMetadataConfig extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $defDescription = new MetadataConfig();
        $defDescription->setName('Beschreibung');
        $defDescription->setDefinition($this->getReference('meta-textarea'));
        $defDescription->setDisplayOrder(1);
        
        $defType = new MetadataConfig();
        $defType->setName('Typ');
        $defType->setDefinition($this->getReference('meta-select'));
        $defTypeOption1 = new MetadataConfigOption();
        $defTypeOption1->setMetadataConfig($defType);
        $defTypeOption1->setName('Vorlesung');
        $defTypeOption2 = new MetadataConfigOption();
        $defTypeOption2->setMetadataConfig($defType);
        $defTypeOption2->setName('Vortrag');
        $defType->getOptions()->add($defTypeOption1);
        $defType->getOptions()->add($defTypeOption2);
        $defType->setDisplayOrder(2);
        
        $manager->persist($defDescription);
        $manager->persist($defType);
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 2;
    }
}
