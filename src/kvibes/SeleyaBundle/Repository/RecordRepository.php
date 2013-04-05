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
                            JOIN r.metadata m
                            JOIN m.config mc
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
