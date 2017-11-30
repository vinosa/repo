<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Vinosa\Repo\Schema ;

/**
 * Description of DbTable
 *
 * @author vinosa
 */
class DbTable
{
    protected $databaseName ;
    protected $name ;
    protected $alias ;
    
    public function __construct($name, $databaseName = null, $alias = null)
    {
        
        $this->name = $name ;
        $this->databaseName = $databaseName ;
        $this->alias = $alias ;
        
    }
    
    public function __toString()
    {
        $str = "";
        
        if( !empty($this->databaseName) ){
            
            $str .= $this->databaseName . "." ;
                
        }
        
        $str .= $this->name ;
        
        if( !empty($this->alias) ){
            
            $str .= " as " . $this->alias ;
                
        }
        
        return $str ;
        
    }
    
    public function getDatabaseName()
    {
        return $this->databaseName ;
    }
    
    public function setDatabaseName( $name )
    {
        $this->databaseName = $name ;
    }
}
