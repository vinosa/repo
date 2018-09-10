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
 * Description of AbstractQuery
 *
 * @author vinosa
 */
class AbstractQuery
{      
    protected $start = 0;
    protected $limit ;
    protected $fields = [] ;
    protected $conditions = [] ;
    protected $defaultLogic = "AND" ;
           
    public function select(array $fields): AbstractQuery
    {
        $new = clone $this ;     
        $new->fields = $fields;      
        return $new ;        
    }
    
    public function getFields(): array
    {
        return $this->fields ;
    }
     
    public function start($start): AbstractQuery
    {
        $new = clone $this ;       
        $new->start = $start ;   
        return $new ;
    }
    
    public function offset($offset): AbstractQuery
    {             
        return $this->start($offset) ;      
    }
    
    public function limit($limit): AbstractQuery
    {
        $new = clone $this ;
        $new->limit = $limit ;        
        return $new ;
    }
    
    public function getStart()
    {
        return $this->start ;
    }
    
    public function getLimit()
    {
        return $this->limit ;
    }
    
    public function getConditions(): array
    {
        return $this->conditions ;
    }
    
    public function withConditions(array $conditions): AbstractQuery
    {
        $new = clone $this ;
        $new->conditions = $conditions ;        
        return $new ;
    }           
        
    public function withCriteria(array $criteria): AbstractQuery
    {
        return $this->withConditions( $this->criteriaToConditions($criteria) );
    }
    
    protected function criteriaToConditions(array $criteria): array
    {
        $conditions = [] ;
        foreach($criteria as $key => $value){
            $conditions = $this->criteriaAutomate($conditions, $key, $value);
        }
        return $conditions ;
    }
        
    protected function criteriaAutomate($conditions,$key,$value) : array
    {
        $logic = $this->defaultLogic;        
        if( (empty($key) || !is_string($key))  && $this->isLogic($value) ){
            $logic = strtoupper($value) ;
        }
        if($this->needsLogic($conditions)){
            $conditions[] = $logic ;
        }
        if(is_string($key)){            
            $conditions[] = new Condition($key, $value, true, $this->defaultOperator);
        }
        if( (empty($key) || !is_string($key)) && is_array($value) ){
            $conditions[] = $this->arrayToCondition($value);
        }
        return $conditions ;    
    }
    
    protected function arrayToCondition($arr)
    {
        if(is_string($arr[0])){
            if(count($arr)<3){
                throw new QueryException("invalid query item: " . print_r($arr,true) . " should be [field,operator,value] ");
            }
            $escape = true;
            if(isset($arr[3])){
                $escape = $arr[3] ;
            }
            return new Condition($arr[0], $arr[2], $escape, $arr[1]) ; 
        }
        $res = [];
        foreach($arr as $key => $value){
            $res = $this->criteriaAutomate($res,$key, $value);
        }
        return $res ;
    }
    
    protected function isLogic($value): bool
    {
        if(is_string($value) && (strtolower($value) == "or" || strtolower($value) == "and")){
            return true ;
        }
        return false ;
    }
    
    protected function needsLogic($conditions)
    {
        $count = count($conditions) ;
        if($count>0 && !$this->isLogic($conditions[$count-1])){
            return true;
        }
        return false;
    }
    
}
class QueryException extends \Exception
{
    
}
