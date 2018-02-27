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
 * Description of AbstractGenericEntity
 *
 * @author vinosa
 */
abstract class AbstractEntity
{
    protected $fields = [];
    protected $source = null ;
       
    public function __set($name, $value)
    {
        
        $this->fields[$name] = $value ;
    }
    
    public function __get( $name )
    {
              
        if( isset($this->fields[$name]) ){
            
            return $this->fields[$name] ;
        } 
        
        throw new EmptyFieldException ("unset field " . $name . " " . $this  ) ;
        
    }
    
    public function setSource(RepositoryInterface $source)
    {
        $this->source = $source ;
    }
    
    public function __toString()
    {
         $str = get_class( $this ) . " :\n" ;
         
         foreach($this->fields as $key => $value){
             
             $str .= "\t" . $key . ":\t" . $value . "\n" ;
                        
         }
         
         $str .= "\n" ;
         
         return $str ;
    }
}
