<?php

namespace kvibes\SeleyaBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BookmarkRepository extends EntityRepository
{
    public function getBookmarkForRecord($record, $user)
    {
        $bookmark = $this->findOneBy(
            array(
                'record' => $record,
                'user'   => $user
            )
        );
        
        return $bookmark;
    }
}
