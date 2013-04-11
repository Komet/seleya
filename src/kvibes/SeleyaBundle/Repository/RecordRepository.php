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
    
    public function getRecordCountForFaculty($facultyId)
    {
        return $this->createQueryBuilder('r')
                    ->select('count(r)')
                    ->where('r.faculty=:faculty_id')
                    ->andWhere('r.visible=1')
                    ->setParameter('faculty_id', $facultyId)
                    ->getQuery()
                    ->getSingleScalarResult();
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
