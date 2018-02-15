<?php

/*
 * Copyright (C) 2017 vinosa
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Vinosa\Repo\Tools;

/**
 * Description of Logger
 *
 * @author vinosa
 */
class Logger implements LoggerInterface
{
    protected $activated = true ;
    protected $timer ;
    
    public function __construct()
    {
       
        
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
    
    public function error($txt)
    {
        $this->log( $txt ) ;
    }
    
    private function log($txt)
    {
        echo $txt ;
    }
}
