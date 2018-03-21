<?php

/*
 * Copyright (C) 2018 vinosa
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
 * Description of LoggableTrait
 *
 * @author vinosa
 */
trait LoggableTrait
{
    protected function debug( $message )
    {
        if( !is_null($this->logger) ){
            
            $this->logger->debug( "DEBUG from " . get_class($this) . " : " . $message ) ;
            
        }
    }
    
    protected function error( $message )
    {
        if( !is_null($this->logger) ){
            
            $this->logger->error( "ERROR from " . get_class($this) . " : " . $message ) ;
            
        }
    }
}
