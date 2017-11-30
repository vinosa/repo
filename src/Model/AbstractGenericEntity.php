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
use Vinosa\Repo\Schema\DbSchema ;
use Vinosa\Repo\Exceptions\EmptyFieldException ;

/**
 * Description of AbstractGenericEntity
 *
 * @author vinosa
 */
class AbstractGenericEntity
{
    
    public function getDbSchema()
    {
              
        return (new DbSchema() )->primary( "id" )
                                 ;
        
    }
    
    protected function querySql(SqlQueryBuilder $query )
    {
        $schema = $this->getDbSchema() ;
        
        if( !is_null( $schema->getTable()  ) ){
            
            $query = $query->insertTo( $schema->getTable() )
                            ->from( $schema->getTable() ) ;
            
        }
                    
        foreach($schema->getWriteableFields() as  $key ){
            
            try{
                $value = $query->quote( $this->__get($key) );
            
                if( $schema->isRelational( $key ) ){
                
                    $value = $this->__get($key) ;
                 
                }
            
                $query->insert["`{$key}`"] = $value ;
                       
                if( !$schema->isPrimaryKey($key) ){
                
                    $query->update["`{$key}`"] =  "`{$key}`=" . $value;
                }
            } 
            catch (EmptyFieldException $ex) {
            }         
                       
        }
               
        foreach($schema->getPrimaryKeys() as $key ){
            try{
                
                $query = $query->where($key, $this->__get( $key ) )  ;
            } 
            catch (EmptyFieldException $ex) {
            }
             
        }
               
        return $query ;
    }
    
    
    public function query(QueryBuilderInterface $query)
    {
        if(is_a($query, SqlQueryBuilder::class)){
            
            return $this->querySql( $query );
            
        }
        if(is_a($query, SolrQueryBuilder::class)){
            
            return $this->querySolr( $query );
            
        }
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
