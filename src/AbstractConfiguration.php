<?php

/*
 * Copyright (C) 2018 vinosa
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

namespace Vinosa\Repo;

/**
 * Description of AbstractConfiguration
 *
 * @author vinosa
 */
abstract class AbstractConfiguration
{
    protected $config;
    
    public function __construct( $config = [] )
    {
        $this->config = $config;
    }
    
    public function get($name, $defaultValue = null)
    {
        if( isset( $this->config[$name] ) ){
            
            return $this->config[ $name ] ;
        }
        
        if( !is_null($defaultValue) ){
            
            return $defaultValue ;
        }
        
        throw new ConfigurationException("undefined configuration " . $name) ;
    }
    
    public function set($name, $value)
    {
        $this->config[$name] = $value ;
    }
}
