<?php

namespace kvibes\SeleyaBundle\Repository;

use Doctrine\ORM\EntityRepository;

class RecordRepository extends EntityRepository
{
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
