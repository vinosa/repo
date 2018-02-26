<?php

/*
 * Copyright (C) 2018 vinogradov
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

/**
 * Description of DatabaseTable
 *
 * @author vinogradov
 */
class DatabaseTable
{
    protected $databaseName ;
    protected $name ;
    protected $alias ;
    
    protected $fields = array() ;
    protected $readonly = array() ;
    protected $primary = array() ;  
    protected $relational = array();
    protected $unique = [] ;
    protected $unescaped = [] ;
    
    public function __construct($name, $databaseName = null, $alias = null)
    {
        
        $this->name = $name ;
        $this->databaseName = $databaseName ;
        $this->alias = $alias ;
        $this->primary[] = "id" ;
        
    }
    
    public function __toString()
    {
        $str = "";
        
        if( !empty($this->databaseName) ){
            
            $str .= $this->databaseName . "." ;
                
        }
        
        $str .= $this->name ;
              
        return $str ;
        
    }
    
    public function getAliasString()
    {
       if( !empty($this->alias) ){
            
            return " as " . $this->alias ;
                
        } 
        return "" ;
    }
    
    
    public function field( $name )
    {
        if( !empty($this->alias) ){
            
            return $this->alias . "." . $name ;
        }
        
        return (string) $this . "." . $name ;
    }
    
    public function getDatabaseName()
    {
        return $this->databaseName ;
    }
    
    public function setDatabaseName( $name )
    {
        $this->databaseName = $name ;
    }
    
    public function columns( $columns )
    {
        $this->fields = $columns ;
        
        return $this ;
    }
    
    public function primary( $column )
    {
        $this->primary[] = $column ;
        
        return $this ;
    }
    
    public function unique( $column )
    {
        $this->unique[] = $column ;
        
        return $this ;
    }
    
    public function readOnly( $column )
    {
        $this->readonly[] = $column ;
        
        return $this ;
    }
    
    public function relational( $column )
    {
        $this->relational[] = $column ;
        
        return $this ;
    }
    
    public function getTable()
    {
        return $this->table ;
    }
    
    public function getFields()
    {
        return $this->fields ;
    }
    
    public function getWriteableFields()
    {
        return array_diff( $this->fields, $this->readonly ) ;
    }
    
    public function isPrimaryKey($field)
    {
        return in_array($field, $this->primary ) ;
    }
    
     public function isUnique($field)
    {
      
        return \in_array($field, $this->getUnique() );
        
    }
    
    public function isRelational( $field )
    {
        return in_array($field, $this->relational ) ;
    }
              
    public function getPrimaryKeys()
    {
        return $this->primary ;
    }
    
    public function getUnique()
    {
        return array_merge( $this->primary, $this->unique ) ;
    }
    
    public function getName()
    {
        return $this->name ;
    }
    
    public function unescaped($field)
    {
        $this->unescaped[] = $field ;
        
        return $this ;
    }
    
    public function isUnescaped($field)
    {
        return in_array($field, $this->unescaped) ;
    }
}
