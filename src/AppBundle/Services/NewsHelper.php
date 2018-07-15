<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;



class NewsHelper
{

    protected $em;

    public function __construct (EntityManager $em) {
        $this->em = $em;
    }

    public function getNews($url){
        // get feedio
        $feedIo = \FeedIo\Factory::create()->getFeedIo();

        
        // read a feed
        $result = $feedIo->read($url);

        // // or read a feed since a certain date
        // $result = $feedIo->readSince($url, new \DateTime('-7 days'));

        return $result;
    } 
}

     
   

?>