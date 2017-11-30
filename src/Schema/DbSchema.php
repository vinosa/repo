<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
