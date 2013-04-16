<?php

namespace kvibes\SeleyaBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    public function getCommentsForRecord($recordId, $page = 0, $perPage = 10, $commentsToExclude = array())
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('c')
           ->where('c.record=:record_id')
           ->setParameter('record_id', $recordId);

        if (count($commentsToExclude) > 0) {
            $qb->andWhere('c.id NOT IN (:excludes)')
               ->setParameter('excludes', $commentsToExclude);
        }
        
        return $qb->addOrderBy('c.created', 'DESC')
           ->setFirstResult($page*$perPage)
           ->setMaxResults($perPage)
           ->getQuery()
           ->getResult();
    }
    
    public function getCommentsCountForRecord($recordId)
    {
        return $this->createQueryBuilder('c')
                    ->select('count(c)')
                    ->where('c.record=:record_id')
                    ->setParameter('record_id', $recordId)
                    ->getQuery()
                    ->getSingleScalarResult();
    }
}
