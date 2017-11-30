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

namespace Vinosa\Repo\QueryBuilders ;

use Vinosa\Repo\DbRepository ;
use Vinosa\Repo\Schema\DbTable ;
use Vinosa\Repo\Exceptions\QueryBuilderException ;

/**
 * Description of SqlQueryBuilder
 *
 * @author vinosa
 */
class SqlQueryBuilder extends AbstractQueryBuilder
{
    public $insert = array();
    public $update = array();
    protected $join = "" ;
    protected $insertTo ;
    protected $from = array() ;
    
    
    public function __construct(DbRepository $repository)
    {
        parent::__construct( $repository );
        
    }
    
     public function from(DbTable $table)
    {
        
        $this->from[] = (string) $this->checkDatabaseName( $table ) ;
        
        return $this ;
    }
    
    public function join(DbTable $table)
    {
        $this->join .= " JOIN " . (string) $this->checkDatabaseName( $table ) ;
        
        return $this ;
    }
    
    public function on($left, $right)
    {
        $this->join .= " ON " . $left . " = " . $right  ;
        
        return $this ;
    }
    
    public function insertTo(DbTable $table)
    {
        $this->insertTo = $this->checkDatabaseName($table) ;
        
        return $this ;
    }
    
    public function subquery()
    {
        return new self( $this->getRepository() ) ;
    }
    
    public function getDatabaseName()
    {
        return $this->repository->getDatabaseName() ;
    }
         
        
    public function getQuerySelect()
    {
                          
        $q = "SELECT " . $this->getSelect() . 
                   
         " FROM " . $this->getFrom() ;
        
        try{
            
            $q .= " WHERE " . $this->whereClause->output();           
            
        } catch (QueryBuilderException $ex) {

        }
        
        $q .= " LIMIT " . $this->start . "," . $this->limit ;
        
        return $q ;
    }
    
    
    public function getQueryInsert()
    {
             
        return "INSERT INTO " . $this->getTable() .  
            
                " (" . implode(", " , array_keys( $this->insert) ) . ") " .
            
                "VALUES (" . implode(", ", $this->insert ) . ") " .
            
                "ON DUPLICATE KEY UPDATE " . implode(", ", $this->update) ;
    }
    
    public function getQueryUpdate()
    {
          
        $q = "UPDATE " . $this->getTable() . " SET ";
           
        $q .= implode(", ", $this->update) ;
        
        $q .= " WHERE " . $this->whereClause->output() ;
        
        return $q ;
    }
    
    public function getQueryDelete()
    {
        $q = "DELETE FROM " . $this->getTable() ;  
        
        $q .= " WHERE " . $this->whereClause->output() ;
        
        return $q ;
    }
    
    public function getQueryCount()
    {
        $q = "SELECT count(*) FROM " . $this->getFrom() ;  
        
        try{
            
            $q .= " WHERE " . $this->whereClause->output();           
            
        } catch (QueryBuilderException $ex) {

        }
        
        return $q ;
    }
    
    private function getFrom()
    {
        
        $str = implode(", " , $this->from ) ;
        
        $str .= $this->join ;
        
        return $str ;
        
    }
    
    private function checkDatabaseName(DbTable $table)
    {
        if( empty($table->getDatabaseName() ) ){
            
            $table->setDatabaseName( $this->getDatabaseName() ) ;
            
        }
        
        return $table ;
    }
    
    private function getSelect()
    {
        $str = "*" ;
        
        if( count( $this->getFields() ) > 0 ){
            
            $str = implode(", " , $this->getFields() );
            
        }
        
        return $str ;
    }
    
    private function getTable()
    {
        if(empty($this->insertTo)){
            
            throw new QueryBuilderException( "Empty Table Name" );
            
        }
        
        return (string) $this->insertTo ;
    }
}
