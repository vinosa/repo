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

/**
 * Description of SolrRepository
 *
 * @author vinosa
 */
class SolrRepository implements RepositoryInterface
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
              
        $query = $this->createNew()
                      ->query( $query ) ;
                                                     
        
        $result = $this->service->fetch( $query  ) ;

        $entities = [];
        
        foreach($result as $doc){

            $entities[] = $this->createNewFromDoc( $doc ) ;
        }

        return $entities ; 
        
    }
    
    public function get( QueryInterface $query)
    {
              
        $query = $this->createNew()
                      ->query( $query ) ;
                                                     
        
        $result = $this->service->fetch( $query  ) ;

        
        foreach($result as $doc){

            $entitiy = $this->createNewFromDoc( $doc ) ;
            
            return $entity ; 
        }
       
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
    
    protected function createNew( )
    {
             
       if(!is_null($this->prototype)) { 
           
            $new = clone $this->prototype ;
       
       }
       else{
           
           $new = new $this->class ;
           
       }
       
       $new->setSource( $this ) ;
       
       return $new ;
             
    }
    
    protected function createNewFromDoc( $doc )
    {
        $new = $this->createNew( ) ;
        
        foreach($doc as $key => $value){
            
            $new->__set($key, $value) ;
        }
        
        return $new ;
    }
}
