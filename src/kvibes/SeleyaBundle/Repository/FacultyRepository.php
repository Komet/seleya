<?php

namespace kvibes\SeleyaBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FacultyRepository extends EntityRepository
{
    public function getFacultiesWithRecords()
    {
        $faculties = array();
        $qb = $this->getEntityManager()->createQueryBuilder();
        $results = $qb->select('f, count(r)')
                      ->from('SeleyaBundle:Faculty', 'f')
                      ->join('f.courses', 'c')
                      ->join('c.records', 'r')
                      ->where('r.visible=1')
                      ->groupBy('f')
                      ->orderBy('f.name', 'ASC')
                      ->getQuery()
                      ->getResult();

        foreach ($results as $result) {
            $faculties[] = array(
                'data'        => $result[0],
                'recordCount' => $result[1]
            );
        }

        return $faculties;
    }
}
