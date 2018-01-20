<?php
namespace App\Controller;
use Think\Controller\RestController;

class CacheController extends RestController{
    public function cacheTest(){
        $memcache = new \Memcache();
        if($memcache->connect("127.0.0.1")){
            echo "Memcache connect";
        }else{
            echo "Memcache Miss connect";
        }
        $memcache->set("words","what people say?",MEMCACHE_COMPRESSED,50);
        echo "value = ".$memcache->get("words")."<br>";
        memcache_flush();
        var_dump($memcache->getstats());


    }
}