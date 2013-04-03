<?php

namespace kvibes\SeleyaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use kvibes\SeleyaBundle\Entity\MetadataConfigDefinition;

class LoadMetadataConfigDefinitions extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $defShortText = new MetadataConfigDefinition();
        $defShortText->setName('Kurztext');
        $defShortText->setId('text');
        $defLongText = new MetadataConfigDefinition();
        $defLongText->setName('Text');
        $defLongText->setId('textarea');
        $defNumber = new MetadataConfigDefinition();
        $defNumber->setName('Zahl');
        $defNumber->setId('number');
        $defUrl = new MetadataConfigDefinition();
        $defUrl->setName('URL');
        $defUrl->setId('url');
        $defDate = new MetadataConfigDefinition();
        $defDate->setName('Datum');
        $defDate->setId('date');
        $defTime = new MetadataConfigDefinition();
        $defTime->setName('Uhrzeit');
        $defTime->setId('time');
        $defDateTime = new MetadataConfigDefinition();
        $defDateTime->setName('Datum und Uhrzeit');
        $defDateTime->setId('datetime');
        $defSelect = new MetadataConfigDefinition();
        $defSelect->setName('Optionen');
        $defSelect->setId('select');
        $defCheckBox = new MetadataConfigDefinition();
        $defCheckBox->setName('Checkbox');
        $defCheckBox->setId('checkbox');
        
        $manager->persist($defShortText);
        $manager->persist($defLongText);
        $manager->persist($defNumber);
        $manager->persist($defUrl);
        $manager->persist($defDate);
        $manager->persist($defDateTime);
        $manager->persist($defCheckBox);
        $manager->persist($defSelect);

        $manager->flush();
        
        $this->addReference('meta-text', $defShortText);
        $this->addReference('meta-textarea', $defLongText);
        $this->addReference('meta-number', $defLongText);
        $this->addReference('meta-url', $defLongText);
        $this->addReference('meta-date', $defDate);
        $this->addReference('meta-time', $defTime);
        $this->addReference('meta-datetime', $defDateTime);
        $this->addReference('meta-checkbox', $defCheckBox);
        $this->addReference('meta-select', $defSelect);
    }
    
    public function getOrder()
    {
        return 1;
    }
}
