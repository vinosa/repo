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
