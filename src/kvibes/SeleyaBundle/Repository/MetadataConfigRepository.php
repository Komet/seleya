<?php

namespace kvibes\SeleyaBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MetadataConfigRepository extends EntityRepository
{
    public function findAllOrdered()
    {
        return $this->getEntityManager()
                    ->createQuery('SELECT m FROM SeleyaBundle:MetadataConfig m ORDER BY m.displayOrder ASC')
                    ->getResult();
    }
    
    public function getNextDisplayOrder()
    {
        $metadataConfigs = $this->findAllOrdered();
        if ($metadataConfigs === null) {
            return 0;
        }
        $lastDisplayOrder = end($metadataConfigs)->getDisplayOrder();
        return $lastDisplayOrder+1;        
    }
}
