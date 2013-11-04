<?php

namespace kvibes\SeleyaBundle\Model;

class Matterhorn
{
    private $url;
    private $username;
    private $password;
    private $handler;
    
    public function __construct($url, $username, $password)
    {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
        
        $this->handler = curl_init();
        curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->handler, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($this->handler, CURLOPT_USERPWD, $this->username.':'.$this->password);
        curl_setopt($this->handler, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($this->handler, CURLOPT_HTTPHEADER, array('X-Requested-Auth: Digest'));
    }
    
    public function __destruct()
    {
        curl_close($this->handler);
    }
    
    public function getEpisodes()
    {
        $episodes = array();
        $itemsPerRequest = 100;
        $offset = 0;
        $total = 0;
        do {
            $data = $this->getJson('episode/episode.json?limit=' . $itemsPerRequest . '&offset=' . $offset);
            $total = $data->{"search-results"}->total;
            $offset += $itemsPerRequest;
            foreach ($data->{"search-results"}->result as $key => $value) {
                $previewImage = null;
                foreach ($value->mediapackage->attachments->attachment as $aKey => $aValue) {
                    if (isset($aValue->ref) && substr($aValue->ref, 0, 10) == 'attachment' &&
                        $aValue->type == 'presenter/player+preview' &&
                        $aValue->mimetype == 'image/jpeg') {
                        $previewImage = $aValue->url;
                        break;
                    }
                }
                $episodes[] = array(
                    'id'      => $value->id,
                    'title'   => $value->dcTitle,
                    'created' => new \DateTime($value->dcCreated),
                    'preview' => $previewImage
                );
            }
            
        } while ($total > $offset);
        
        return $episodes;
    }
    
    private function getJson($serviceUrl, $method = 'get', $data = null)
    {
        if (!$this->checkMatterhornService()) {
            throw new \Exception('Connection to Matterhorn failed');
        }
        
        $options = array(
            CURLOPT_URL => $this->url . '/' . $serviceUrl,
            CURLOPT_FRESH_CONNECT => 1
        );
        switch ($method) {
            case 'post':
                $options[CURLOPT_POST] = 1;
                if ($data !== null) {
                    $options[CURLOPT_POSTFIELDS] = $data;
                }
                break;
            default:
                $options[CURLOPT_HTTPGET] = 1;
                break;
        }
        
        curl_setopt_array($this->handler, $options);
        $response = curl_exec($this->handler);
        $httpCode = curl_getinfo($this->handler, CURLINFO_HTTP_CODE);
        
        if ($httpCode == 404) {
            return false;
        } else {
            return json_decode($response);
        }
    }
    
    private function checkMatterhornService()
    {
        if (@fsockopen(str_replace('http://', '', $this->url))) {
            return true;
        }
        return false;
    }
}
