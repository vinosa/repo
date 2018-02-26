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

namespace Vinosa\Repo ;



/**
 * Description of WhereClause
 *
 * @author vinosa
 */
class AbstractWhereClause
{

    private $wheres = [];
    private $query ;
      
    public function __construct(QueryInterface $query )
    {
        $this->query = $query ;
    }
    
    
    public function output()
    {
       
        if(count($this->wheres) == 0){
            
            throw new QueryException("empty where clause") ;
            
        }
        
        return (string) $this ;
        
    }
    
    
    public function __toString()
    {
       
        $str = "";
        
        foreach($this->wheres as $where){
            
            if( is_string($where) && strlen($where)>0 ){
                
                $str .= " " . $where;
                
            }
            
            if( is_object($where) && strlen( (string) $where ) > 0  ){
                                              
                $str .= " (" . (string) $where . ")" ;
                                           
            }
        }
               
        return $str ;
    }
    
    public function where($col, $value = null, $operator = false, $logical = "AND" )
    {
              
        return $this->whereSafe($col, $this->quote( $value ), $operator, $logical) ; 
        
    }
    
    public function whereSafe($col, $value = null, $operator = false, $logicalOperator = "AND" )
    {
        if( is_object($col) ){
            
            if(is_a($col, AbstractWhereClause::class)){
                
                $where = $col ;
            }
            
            if(is_a($col, QueryInterface::class)){
                
                $where = $col->getClause() ;
            
            }
                      
            if( strlen( (string) $where ) == 0){
                
                return $this ;
                
            } 
            
            $this->addLogicalOperator( $logicalOperator );
            
            $this->wheres[] = $where ;
            
            return $this ;
            
        }
               
        $this->addLogicalOperator( $logicalOperator );
              
        $this->wheres[] = $col . $this->operator( $operator ) . $value ;
        
        return $this ;
    }
    
    public function andWhere($col, $val = null, $operator = false)
    {
        
        return $this->where($col, $val, $operator, "AND");
        
    }
    
    public function orWhere($col, $val = null, $operator = false)
    {
              
        return $this->where($col, $val, $operator, "OR");
        
    }
    
    public function andWhereSafe($col, $val = null, $operator = false)
    {
        
        return $this->whereSafe($col, $val, $operator, "AND");
        
    }
    
    public function orWhereSafe($col, $val = null, $operator = false)
    {
        
        return $this->whereSafe($col, $val, $operator, "OR");
        
    }
       
    protected function quote( $var )
    {
        if( $var === null){
            
            return $var;
            
        }
        return $this->query->quote( $var );
    }
    
    
    
    protected function addLogicalOperator( $logicalOperator )
    {
        if( count($this->wheres) > 0 ){
            
            $this->wheres[] = $logicalOperator ;
            
        }
    }
    
           
}
