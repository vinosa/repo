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

use Vinosa\Repo\RepositoryInterface ;
/**
 * Description of AbstractQuery
 *
 * @author vinosa
 */
abstract class AbstractQuery implements QueryInterface
{
    //put your code here
    protected $repository ;
       
    protected $start = 0;
    protected $limit = 10;
    protected $whereClause ;
    protected $fields = array() ;
       
    
    public function __call($name, $arguments)
    {
        if( strpos( strtolower($name), "where" ) !== false ){
            
            call_user_func_array(array( $this->getWhere(), $name), $arguments ) ;
            
        }
        
        if($name == "get" || $name == "fetch" || $name == "count"){
            
            array_unshift($arguments, $this) ;
            
            return call_user_func_array(array( $this->getRepository(), $name), $arguments ) ;
            
        }
        
        return $this ;
    }
    
    public function getRepository()
    {
        return $this->repository ;
    }
    
    public function select($fields)
    {
        if(is_array($fields)){
            
            $this->fields = $fields;
                
        }
        
        if(is_string($fields)){
            
            $this->fields = explode("," , $fields);
        }
        
        return $this ;
    }
    
    public function getFields()
    {
        return $this->fields ;
    }
    
    public function withFields(array $fields)
    {
             
        $this->fields = $fields ;
        
        return $this ;
    }
    
    
    
    public function start($start)
    {
        $this->start = $start ;
        
        return $this ;
    }
    
    public function offset($offset)
    {
              
        return $this->start($offset) ;
        
    }
    
    public function limit($limit)
    {
        $this->limit = $limit ;
        
        return $this ;
    }
    
    public function getStart()
    {
        return $this->start ;
    }
    
    public function getLimit()
    {
        return $this->limit ;
    }
           
    public function quote( $unsafeString )
    {
        
        return $this->getRepository() ->quote( $unsafeString );
        
    }
    
    protected function getWhere()
    {
        return $this->whereClause ;
    }
    
    
    
        
}
