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

namespace Vinosa\Repo\Database;


use Vinosa\Repo\EmptyFieldException ;
use Vinosa\Repo\AbstractEntity ;

/**
 * Description of DatabaseGenericEntity
 *
 * @author vinosa
 */
class DatabaseGenericEntity extends AbstractEntity implements DatabaseEntityInterface
{
    
    public function query(SqlQuery $query)
    {
        if(!method_exists($this, "getTable")){
            
            return $query ;
            
        }
        
        $table = $this->getTable() ;
                             
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
}
