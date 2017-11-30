<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Vinosa\Repo\Tools;

/**
 * Description of Timer
 *
 * @author vinosa
 */
class Timer
{
     protected $start ;
    protected $end ;
    protected $counting ;
    
    public function __construct()
    {
        $this->start();
        $this->end();
        $this->counting = false ;
    }
    
    public function start()
    {
        $this->start = microtime(true) ;
        $this->counting = true ;
    }
    
    public function end()
    {
        if($this->counting){
            
             $this->end = microtime(true);
             
        }
        $this->counting = false ;
    }
    
    public function getElapsed()
    {
        if($this->counting){
            
            $this->end() ;
            
        }
        
        return $this->end - $this->start ;
    }
    
    public function __toString()
    {
        return $this->getElapsed() . " secs" ;
    }
}
