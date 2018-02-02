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

namespace Vinosa\Repo\Model;

use Vinosa\Repo\QueryBuilders\SqlQueryBuilder ;
use Vinosa\Repo\QueryBuilders\QueryBuilderInterface ;
use Vinosa\Repo\Schema\DbTable ;
use Vinosa\Repo\Exceptions\EmptyFieldException ;

/**
 * Description of AbstractGenericEntity
 *
 * @author vinosa
 */
class AbstractGenericEntity
{
    
    
    public function query(SqlQueryBuilder $query)
    {
        $table = $this->getTable() ;
        
        if( is_null($table) ){
            
            return $query ;
        }
                     
        $query = $query->flushTables()
                       ->from( $this->getTable() ) ;
                               
        foreach($table->getWriteableFields() as  $key ){
            
            try{
                $value = $query->quote( $this->__get($key) );
            
                if( $table->isUnescaped( $key ) ){
                
                    $value = $this->__get($key) ;
                 
                }
            
                $query->insert["`{$key}`"] = $value ;                      
           
                if( !$table->isUnique($key) ){
                
                    $query->update["`{$key}`"] =  "`{$key}`=" . $value;
                }
            } 
            catch (EmptyFieldException $ex) {
            }         
                       
        }
               
        foreach($table->getUnique() as $key ){
            try{
                
                $query = $query->where($key, $this->__get( $key ) )  ;
            } 
            catch (EmptyFieldException $ex) {
            }
             
        }
               
        return $query ;
    }
      
    
    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }
    
    public function __get( $name )
    {
        if( isset($this->{$name}) ){
            
            return $this->{$name} ;
        }   
        
        throw new EmptyFieldException ("unset field " . $name . print_r($this,true) ) ;
        
    }
}
