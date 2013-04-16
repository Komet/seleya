<?php

namespace kvibes\SeleyaBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CourseRepository extends EntityRepository
{
    public function getCoursesInFaculty($facultyId, $limit, $offset)
    {
        $courses = array();
        $qb = $this->getEntityManager()->createQueryBuilder();
        $results = $qb->select('c, count(r)')
                      ->from('SeleyaBundle:Course', 'c')
                      ->join('c.records', 'r')
                      ->where('r.visible=1')
                      ->andWhere('c.faculty=:facultyId')
                      ->setParameter('facultyId', $facultyId)
                      ->groupBy('c')
                      ->orderBy('c.name', 'ASC')
                      ->setFirstResult($offset)
                      ->setMaxResults($limit)
                      ->getQuery()
                      ->getResult();

        foreach ($results as $result) {
            $courses[] = array(
                'data'        => $result[0],
                'recordCount' => $result[1]
            );
        }

        return $courses;
    }
    
    public function getCoursesInFacultyOrderedByRecordDate($facultyId, $limit, $offset)
    {
        $courses = array();
        
        $qb = $this->getEntityManager()->createQueryBuilder();
        $results = $qb->select('c, MAX(r.recordDate) AS latestRecordDate, COUNT(r)')
                      ->from('SeleyaBundle:Course', 'c')
                      ->join('c.records', 'r')
                      ->where('r.visible=1')
                      ->andWhere('c.faculty=:facultyId')
                      ->setParameter('facultyId', $facultyId)
                      ->groupBy('c')
                      ->orderBy('latestRecordDate', 'DESC')
                      ->setFirstResult($offset)
                      ->setMaxResults($limit)
                      ->getQuery()
                      ->getResult();
        foreach ($results as $result) {
            $courses[] = array(
                'data'        => $result[0],
                'recordCount' => $result[1]
            );
        }

        return $courses;
    }

    public function getCourseCountForFaculty($facultyId)
    {
        $qb = $this->createQueryBuilder('r');
        return $this->createQueryBuilder('c')
                    ->select('count(c)')
                    ->where('c.faculty=:faculty_id')
                    ->andWhere($qb->expr()->exists('SELECT r FROM SeleyaBundle:Record r WHERE r.course=c.id AND r.visible=1'))
                    ->setParameter('faculty_id', $facultyId)
                    ->groupBy('c.faculty')
                    ->getQuery()
                    ->getSingleScalarResult();
    }
    
    public function getAllCoursesQuery()
    {
        $qb = $this->createQueryBuilder('c');
        return $qb->select('c')
                  ->getQuery();
    }
}
