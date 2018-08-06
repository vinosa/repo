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
    protected $limit = 10;
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
              
    public function where($field, $value=null, string $operator = null, bool $escape = true, string $logic = null )
    {
        if(!is_a($field, AbstractQuery::class) && ( empty($value) || is_null($value) ) ){           
            throw new QueryException("value can't be empty in where statement " . print_r($this,true) ) ;
        }       
        if( is_null($logic) ){       
            $logic = $this->defaultLogic ;
        }      
        $conditions = $this->getConditions() ;       
        if(count($this->conditions) > 0){        
            $conditions[] = $logic ;
        }     
        if( \is_a($field, AbstractQuery::class) ){
            
            $conditions[] = $field->conditions ;
        }
        else{
        
            $conditions[] = new Condition($field, $value, $escape, $operator) ;       
        }    
        return $this->withConditions($conditions) ;             
    }
    
    public function whereSafe($field, $value = null, $operator = null, $logic = null )
    {
        return $this->where($field, $value, $operator, false, $logic) ;
    }
    
    public function orWhereSafe($field, $value = null, $operator = null)
    {
        return $this->where($field, $value, $operator, false, "OR") ;
    }
    
    public function orWhere($field, $value = null, $operator = null)
    {
        return $this->where($field, $value, $operator, true,  "OR") ;
    }
       
}
class QueryException extends \Exception
{
    
}
