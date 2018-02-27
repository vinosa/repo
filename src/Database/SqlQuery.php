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

namespace Vinosa\Repo\Database ;

use Vinosa\Repo\AbstractQuery ;
use Vinosa\Repo\QueryException ;


/**
 * Description of SqlQuery
 *
 * @author vinosa
 */
class SqlQuery extends AbstractQuery
{
     
    public $insert = [];
    public $update = [];
    protected $join = "" ;
    protected $tables = [] ;
    
    
    public function __construct(DatabaseRepository $repository)
    {
        $this->repository = $repository ;
        
        $this->whereClause = new SqlWhereClause( $this ) ;
        
    }
    
     public function from(DatabaseTable $table)
    {
                    
        if( !in_array( $this->checkDatabaseName( $table ), $this->tables ) ){
            
            $this->tables[] = $table ;
            
        }
        
        return $this ;
    }
    
    public function flushTables()
    {
        $this->tables = [];
        
        return $this ;
    }
    
    public function join(DatabaseTable $table)
    {
        $this->join .= " JOIN " . (string) $this->checkDatabaseName( $table ) . $table->getAliasString() ;
        
        return $this ;
    }
    
    public function leftOuterJoin(DatabaseTable $table)
    {
        $this->join .= " LEFT OUTER JOIN " . (string) $this->checkDatabaseName( $table ) . $table->getAliasString() ;
        
        return $this ;
    }
    
    public function on($left, $right)
    {
        $this->join .= " ON " . $left . " = " . $right  ;
        
        return $this ;
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
            
            $q .= " WHERE " . $this->getWhere()->output();           
            
        } catch (QueryException $ex) {

        }
        
        $q .= " LIMIT " . $this->start . "," . $this->limit ;
        
        return $q ;
    }
    
    
    public function getQueryInsert()
    {
             
        return "INSERT INTO " . $this->getTableStr() .  
            
                " (" . implode(", " , array_keys( $this->insert) ) . ") " .
            
                "VALUES (" . implode(", ", $this->insert ) . ") " .
            
                "ON DUPLICATE KEY UPDATE " . implode(", ", $this->update) ;
    }
    
    public function getQueryUpdate()
    {
          
        $q = "UPDATE " . $this->getTableStr() . " SET ";
           
        $q .= implode(", ", $this->update) ;
        
        $q .= " WHERE " . $this->getWhere()->output() ;
        
        return $q ;
    }
    
    public function getQueryDelete()
    {
        $q = "DELETE FROM " . $this->getTableStr() ;  
        
        $q .= " WHERE " . $this->getWhere()->output() ;
        
        return $q ;
    }
    
    public function getQueryCount()
    {
        $q = "SELECT count(*) FROM " . $this->getFrom() ;  
        
        try{
            
            $q .= " WHERE " . $this->getWhere()->output();           
            
        } catch (QueryException $ex) {

        }
        
        return $q ;
    }
    
    public function updateField($field, $value)
    {
        $this->repository->updateField($this, $field, $value );
    }
    
    private function getFrom()
    {
        $from = [] ;
        
        foreach($this->getTables() as $table){
            
            $from[] = (string) $table . $table->getAliasString() ;
        }
        
        $str = implode(", " , $from ) ;
        
        $str .= $this->join ;
                
        return $str ;
        
    }
    
    private function checkDatabaseName(DatabaseTable $table)
    {
        if( empty($table->getDatabaseName() ) ){
            
            $table->setDatabaseName( $this->getDatabaseName() ) ;
            
        }
        
        return $table ;
    }
    
    protected function getSelect()
    {
        $str = $this->getTableStr() . ".*" ;
        
        if( count( $this->getFields() ) > 0 ){
            
            $str = implode(", " , $this->getFields() );
            
        }
        
        return $str ;
    }
    
    protected function getTable()
    {
       
        if( count($this->tables) == 0 ){
            
            throw new QueryException( "Empty Table " . print_r($this,true) );
            
        }
        
        return $this->tables[0] ;
    }
    
    protected function getTableStr()
    {
        
        return (string) $this->checkDatabaseName( $this->getTable() ) ;
        
    }
    
    protected function getTables()
    {
        return $this->tables ;
    }
        
}
