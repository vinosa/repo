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
 * Description of DatabaseConfiguration
 *
 * @author vinogradov
 */
class DatabaseConfiguration
{
    protected $config = [];
    
    const HOST = "host";
    const USER = "user" ;
    const PASSWORD = "password";
    const DBNAME = "dbname" ;
    
    public function get($name)
    {
        if( isset( $this->config[$name] ) ){
            
            return $this->config[ $name ] ;
        }
        
        throw new \Exception("undefined configuration " . $name) ;
    }
    
    public function set($name, $value)
    {
        $this->config[$name] = $value ;
    }
    
    public function getHost()
    {
        return $this->get( self::HOST ) ;
    }
    
    public function getUser()
    {
        return $this->get( self::USER ) ;
    }
    
    public function getPassword()
    {
        return $this->get( self::PASSWORD ) ;
    }
    
    public function getDatabaseName()
    {
        return $this->get( self::DBNAME ) ;
    }
}
