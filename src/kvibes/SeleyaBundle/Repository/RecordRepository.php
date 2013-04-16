<?php

namespace kvibes\SeleyaBundle\Repository;

use Doctrine\ORM\EntityRepository;

class RecordRepository extends EntityRepository
{
    public function getRecord($id)
    {
        $query = $this->getEntityManager()
                      ->createQuery(
                          'SELECT r, m FROM SeleyaBundle:Record r
                           LEFT JOIN r.metadata m
                           LEFT JOIN m.config mc
                           WHERE r.id = :id
                           ORDER BY mc.displayOrder'
                      )
                      ->setParameter('id', $id);
        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    public function getQueryForAllRecords($visible, $user = null)
    {
        $qb = $this->createQueryBuilder('r');
        if ($user !== null) {
            return $qb->select('r')
                      ->leftJoin('r.users', 'u')
                      ->leftJoin('r.lecturers', 'l')
                      ->where('u=:user')
                      ->orWhere('l=:lecturer')
                      ->andWhere('r.visible=:visible')
                      ->setParameter('user', $user->getId())
                      ->setParameter('lecturer', $user->getId())
                      ->setParameter('visible', $visible)
                      ->groupBy('r')
                      ->orderBy('r.recordDate', 'desc')
                      ->getQuery();
        } else {
            return $qb->select('r')
                      ->where('r.visible=:visible')
                      ->setParameter('visible', $visible)
                      ->groupBy('r')
                      ->orderBy('r.recordDate', 'desc')
                      ->getQuery();
        }
    }
    
    public function findLatest($limit = 10)
    {
        return $this->findBy(
            array('visible' => true),
            array('recordDate' => 'DESC'),
            $limit
        );
    }

    public function getRecordCountForCourse($courseId)
    {
        return $this->createQueryBuilder('r')
                    ->select('count(r)')
                    ->where('r.course=:course_id')
                    ->andWhere('r.visible=1')
                    ->setParameter('course_id', $courseId)
                    ->getQuery()
                    ->getSingleScalarResult();
    }
    
    public function getRecentRecordsInCourse($courseId, $excludeRecord, $limit)
    {
        return $this->createQueryBuilder('r')
                    ->select('r')
                    ->where('r.course=:course_id')
                    ->setParameter('course_id', $courseId)
                    ->andWhere('r.visible=1')
                    ->andWhere('r.id!=:exclude_record')
                    ->setParameter('exclude_record', $excludeRecord)
                    ->orderBy('r.recordDate', 'DESC')
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult();
    }
    
    public function getRecordStats(\DateInterval $interval)
    {
        $from = new \DateTime();
        $from->sub($interval);
        $to = new \DateTime();
        
        $results = $this->createQueryBuilder('r')
                        ->select('r, SUM(v.viewCount), COUNT(b.id), COUNT(c.id)')
                        ->leftJoin('r.views', 'v')
                        ->leftJoin('r.bookmarks', 'b')
                        ->leftJoin('r.comments', 'c')
                        ->where('r.visible=1')
                        ->andWhere('(v.date>=:from AND v.date<=:to) OR (v.date IS NULL)')
                        ->andWhere('(b.created>=:from AND b.created<=:to) OR (b.created IS NULL)')
                        ->andWhere('(c.created>=:from AND c.created<=:to) OR (c.created IS NULL)')
                        ->setParameter('from', $from)
                        ->setParameter('to', $to)
                        ->groupBy('r')
                        ->getQuery()
                        ->getResult();
            
        return $results;
    }
}
