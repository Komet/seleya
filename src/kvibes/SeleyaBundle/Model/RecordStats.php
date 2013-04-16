<?php

namespace kvibes\SeleyaBundle\Model;

class RecordStats
{
    private $doctrine;
    
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    public function getHotRecords($interval, $limit)
    {
        $records = $this->getRecordsWithStats($interval);
        $this->applyWeighting($records);
        usort($records, 'kvibes\SeleyaBundle\Model\RecordStats::compareWeightedRecords');
        $hotRecords = array();
        for ($i=0, $n=count($records); $i<$n && $i<$limit; ++$i) {
            $hotRecords[] = $records[$i][0];
        }
        
        return $hotRecords;
    }
    
    private function getRecordsWithStats($interval)
    {
        $em = $this->doctrine->getManager();
        $records = $em->getRepository('SeleyaBundle:Record')
                      ->getRecordStats($interval);
        $recordsWithStats = array();
        $sumViews = 0;
        $sumComments = 0;
        $sumBookmarks = 0;
        foreach ($records as $record) {
            $sumViews     += $record[1];
            $sumComments  += $record[2];
            $sumBookmarks += $record[3];
        }
        
        foreach ($records as &$record) {
            $record['percentViews'] = ($sumViews != 0) ? $record[1]/$sumViews : 0;
            $record['percentComments'] = ($sumComments != 0) ? $record[2]/$sumComments : 0;
            $record['percentBookmarks'] = ($sumBookmarks != 0) ? $record[3]/$sumBookmarks : 0;
        }
        
        return $records;
    }

    private function applyWeighting(&$records)
    {
        foreach ($records as &$record) {
            $record['weighted'] = $record['percentViews']*0.5 +
                                  $record['percentComments']*0.25 +
                                  $record['percentBookmarks']*0.25;
        }
    }
    
    public static function compareWeightedRecords($a, $b)
    {
        if ($a['weighted'] == $b['weighted']) {
            return 0;
        }
        
        return ($a['weighted'] < $b['weighted']) ? 1 : -1;
    }
}
