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
    protected $from ;
        
    public function __construct(DatabaseRepository $repository)
    {
        $this->repository = $repository ;
        
        $this->whereClause = new SqlWhereClause( $this ) ;
        
    }
      
    public function from( $table )
    {
        $this->from = $table ;
        
        return $this ;
    }
    
    
    public function join($table)
    {
        $this->join .= " JOIN " . $table ;
        
        return $this ;
    }
    
    public function leftOuterJoin($table)
    {
        $this->join .= " LEFT OUTER JOIN " . $table  ;
        
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
                          
        $q = "SELECT " . implode("," , $this->fields) . 
                   
         " FROM " . $this->from . " " . $this->join ;
        
        try{
            
            $q .= " WHERE " . $this->getWhere()->output();           
            
        } catch (QueryException $ex) {

        }
        
        $q .= " LIMIT " . $this->start . "," . $this->limit ;
        
        return $q ;
    }
    
    
    public function getQueryInsert()
    {
             
        return "INSERT INTO " . $this->from .  
            
                " (" . implode(", " , array_keys( $this->insert) ) . ") " .
            
                "VALUES (" . implode(", ", $this->insert ) . ") " .
            
                "ON DUPLICATE KEY UPDATE " . implode(", ", $this->update) ;
    }
    
    public function getQueryUpdate()
    {
          
        $q = "UPDATE " . $this->from . " SET ";
           
        $q .= implode(", ", $this->update) ;
        
        $q .= " WHERE " . $this->getWhere()->output() ;
        
        return $q ;
    }
    
    public function getQueryDelete()
    {
        $q = "DELETE FROM " . $this->from ;  
        
        $q .= " WHERE " . $this->getWhere()->output() ;
        
        return $q ;
    }
    
    public function getQueryCount()
    {
        $q = "SELECT count(*) FROM " . $this->from ;  
        
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
    
   
}
