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
namespace Vinosa\Repo\Schema ;
/**
 * Description of DbSchema
 *
 * @author vinosa
 */
class DbSchema
{
     protected $fields = array() ;
    protected $readonly = array() ;
    protected $primary = array() ;  
    protected $relational = array();
    protected $table ;
    
    public function table(DbTable $table)
    {
        $this->table = $table ;
        
        return $this ;
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
    
    public function isRelational( $field )
    {
        return in_array($field, $this->relational ) ;
    }
              
    public function getPrimaryKeys()
    {
        return $this->primary ;
    }
}
