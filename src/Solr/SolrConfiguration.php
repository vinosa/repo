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
namespace Vinosa\Repo\Solr ;

use Vinosa\Repo\AbstractConfiguration ;
/**
 * Description of SolrConfiguration
 *
 * @author vinosa
 */
class SolrConfiguration extends AbstractConfiguration
{
    const ENDPOINT = "endpoint" ;
    const CORE = "core" ;
    const HOST = "host" ;
    const PORT = "port" ;
    const PATH = "path" ;
    const TIMEOUT = "timeout" ;
    
    public function getEndpoint()
    {
        return $this->get( self::ENDPOINT, "endpoint" ) ;
    }
    
    public function getCore()
    {
        return $this->get( self::CORE ) ;
    }
    
    public function getHost()
    {
        return $this->get( self::HOST ) ;
    }
    
    public function getPort()
    {
        return $this->get( self::PORT ) ;
    }
    
    public function getPath()
    {
        return $this->get( self::PATH ) ;
    }
    
    public function getTimeout()
    {
        return $this->get( self::TIMEOUT, 30 ) ;
    }
    
    public function getConfigurationArray()
    {
        return [$this->getEndpoint() => 
                    [ $this->getCore() => [
                            'host' => $this->getHost(),
                            'port' => $this->getPort(),
                            'path' => $this->getPath(),
                            'core' => $this->getCore(),
                            'timeout' => $this->getTimeout()
                        ]
                    ]
                ];
    }
}
