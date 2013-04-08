<?php

namespace kvibes\SeleyaBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BookmarkRepository extends EntityRepository
{
    public function getBookmarksForUser($user)
    {
        return $this->findBy(
            array(
                'user' => $user
            ),
            array(
                'created' => 'DESC'
            )
        );
    }
    
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
