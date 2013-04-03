<?php

namespace kvibes\SeleyaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use kvibes\SeleyaBundle\Entity\Metadata;
use kvibes\SeleyaBundle\Entity\Record;

class LoadMetadata extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $record = new Record();
        $record->setTitle('Testaufzeichnung 1');
        $record->setRecordDate(new \DateTime('2013-03-23'));
        $record2 = new Record();
        $record2->setTitle('Testaufzeichnung 2');
        $record2->setRecordDate(new \DateTime('2013-03-24'));

        $manager->persist($record);
        $manager->persist($record2);
        $manager->flush();
        
        /*
        $manager->clear();
        $recs = $manager->createQueryBuilder()
                        ->select(array('r', 'm', 'c'))
                        ->from('kvibes\SeleyaBundle\Entity\Record', 'r')
                        ->join('r.metadata', 'm')
                        ->join('m.config', 'c')
                        ->orderBy('c.displayOrder', 'DESC')
                        ->getQuery()
                        ->getResult();
        /*
        foreach ($recs as $rec) {
            echo "RECORD\n";
            foreach ($rec->getMetadata() as $m) {
                echo "  ".$m->getConfig()->getName().": ";
                if ($m->getConfig()->getDefinition()->getId() == 'date') {
                    echo $m->getValue()->format('Y-m-d')."\n";
                } else {
                    echo $m->getValue()."\n";
                }
            }
        }
         * 
         */
        
    }
    
    public function getOrder()
    {
        return 3;
    }
}
