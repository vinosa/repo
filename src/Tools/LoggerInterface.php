<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Vinosa\Repo\Tools;

/**
 * Description of LoggerInterface
 *
 * @author vinosa
 */
interface LoggerInterface
{
    //put your code here
    public function debug( $msg ) ;
    
    public function startTimer();
    
    public function getDuration() ;
}
