<?php

/*
 * Copyright (C) 2018 vinogradov
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

namespace Vinosa\Repo;

/**
 * Description of Timer
 *
 * @author vinosa
 */
class Timer
{
    protected $start ;
    
    public function __construct()
    {
        $this->start = microtime(true) ;     
    }
    
    public function started(): Timer
    {
        $new = clone $this ;
        $new->start = microtime(true) ;
        return $new;
    }
    
    public function seconds()
    {
        return round($this->elapsed(),2) ;
    }
    
    public function __toString()
    {
        $secs = $this->elapsed() ;
        $hours = floor($secs / 3600) ;        
        $secs -= $hours * 3600;
        $mins = floor($secs / 60) ;        
        $secs -= $mins * 60;        
        $str = "";
        if($hours > 0){
            $str .= $hours . " hours ";
        }    
        if($mins > 0){
            $str .= $mins . " mins ";
        }              
        return $str . round($secs,2) . " secs" ;      
    }
    
    public function elapsed()
    {
        return microtime(true) - $this->start ;
    }
}
