<?php

namespace kvibes\SeleyaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use kvibes\SeleyaBundle\Entity\Faculty;

class LoadFaculties extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faculties = array(
            'Medien- und Kommunikationswissenschaften; Sportwissenschaften',
            'Agrar- und Ernährungswissenschaften',
            'Altertumswissenschaften',
            'Anglistik und Amerikanistik',
            'Bibelwissenschaften und Kirchengeschichte',
            'Biochemie und Biotechnologie',
            'Biologie',
            'Chemie',
            'Geowissenschaften und Geographie',
            'Germanistik',
            'Geschichte',
            'Informatik',
            'Katholische Theologie und ihre Didaktik',
            'Kunstgeschichte und Archäologien Europas',
            'Mathematik',
            'Musik',
            'Pharmazie',
            'Philosophie und Ethnologie',
            'Physik',
            'Politikwissenschaft und Japanologie',
            'Psychologie',
            'Pädagogik',
            'Rehabilitationspädagogik',
            'Romanistik',
            'Schulpädagogik und Grundschuldidaktik',
            'Slawistik und Sprechwissenschaft',
            'Soziologie',
            'Systematische Theologie, Praktische Theologie und Religionswissenschaften',
            'Rechtswissenschaft',
            'Medizin',
            'Biowissenschaften',
            'Orientalistik',
            'Sozialwissenschaften und historische Kulturwissenschaften',
            'Erziehungswissenschaften',
            'Theologie',
            'Philologien, Kommunikations- und Musikwissenschaften',
            'Wirtschaftswissenschaften'
        );

        foreach ($faculties as $facultyName) {
            $faculty = new Faculty();
            $faculty->setName($facultyName);
            $manager->persist($faculty);
        }

        $manager->flush();
    }
    
    public function getOrder()
    {
        return 3;
    }
}
