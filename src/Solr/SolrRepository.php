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

namespace Vinosa\Repo\Solr;

use Vinosa\Repo\RepositoryInterface ;
use Vinosa\Repo\QueryInterface ;
use Vinosa\Repo\AbstractRepository ;

/**
 * Description of SolrRepository
 *
 * @author vinosa
 */
class SolrRepository extends AbstractRepository implements RepositoryInterface
{
    protected $service ;
    protected $prototype ;
    protected $class = SolrGenericEntity::class ;
    
    public function __construct(SolrServiceInterface $service, $prototype = null)
    {
        $this->service = $service ;
        $this->prototype = $prototype ;
    }
          
    public function fetch( QueryInterface $query)
    {
                                    
        return array_map( [$this, $this->callbackCreateEntity], $this->service->fetch( $this->createNew()->query($query) ) ) ;
      
    }
    
    public function get( QueryInterface $query)
    {
                                
        return array_map( [$this, $this->callbackCreateEntity], $this->service->fetch( $this->createNew()->query($query) ) )[0] ;
       
    }
    
    public function query()
    {
        return new SolrQuery( $this ) ;
    }
    
    public function quote( $input)
    {
       
        return $this->service->quote( $input );
        
    }
    
    public function getHelper()
    {
        return $this->service->getHelper() ;
    }
    
}
