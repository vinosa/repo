<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Vinosa\Repo\Tools;

/**
 * Description of Logger
 *
 * @author vinosa
 */
class Logger implements LoggerInterface
{
    protected $activated ;
    protected $timer ;
    
    public function __construct($activated = false)
    {
       
        $this->activated = $activated ;
        
         $this->timer = new Timer() ;
    }
    
    public function debug( $msg )
    {
        if($this->activated){
            
            echo( $msg . "\n" ) ;
            
        }
    }
    
    public function startTimer()
    {
        $this->timer->start() ;
    }
    
    public function getDuration()
    {
        $this->timer->end() ;
         
        return (string) $this->timer ;
    }
}
