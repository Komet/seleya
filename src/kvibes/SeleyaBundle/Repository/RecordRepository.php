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
    
    public function findNewOrderedByCreated()
    {
        return $this->findAllOrderedByCreated(false);
    }

    public function findVisibleOrderedByCreated()
    {
        return $this->findAllOrderedByCreated(true);
    }
    
    public function findLatest($limit = 10)
    {
        return $this->findAllOrderedByCreated(true, $limit);
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

    private function findAllOrderedByCreated($visible = true, $limit = null)
    {
        return $this->findBy(
            array(
                'visible' => $visible
            ), 
            array(
                'created' => 'DESC'
            ), 
            $limit
        );
    }
}
