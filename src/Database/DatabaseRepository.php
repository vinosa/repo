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
namespace Vinosa\Repo\Database ;

use Vinosa\Repo\QueryInterface ;
use Vinosa\Repo\RepositoryInterface ;
use Vinosa\Repo\AbstractRepository ;


/**
 * Description of DbRepository
 *
 * @author vinosa
 */
class DatabaseRepository extends AbstractRepository implements RepositoryInterface
{
    
    protected $service ;
    protected $prototype ;
      
    public function __construct(DatabaseServiceInterface $service, DatabaseEntityInterface $entityPrototype )
    {
        
        $this->service = $service;
        
        $this->prototype = $entityPrototype ;
    } 
                   
    public function getDatabaseName()
    {
        return $this->service->getDatabaseName() ;
    }
       
    public function save(DatabaseEntityInterface $entity )
    {     
                
        return $this->service->execute( $entity->query( $this->query() )->getQueryInsert() );
                                                                              
    }
    
    public function update(DatabaseEntityInterface $entity )
    {
                  
        return $this->service ->execute( $entity->query( $this->query() )->getQueryUpdate() );
                                 
    }
    
    public function get(QueryInterface $query )
    {
        
        return array_map( [$this, $this->callbackCreateEntity ], $this->service->fetch( $this->createNew()
                                                                                            ->query($query)
                                                                                            ->limit(1)
                                                                                            ->getQuerySelect()
                                                                                        )
                        )[0] ;
                    
    }
    
    public function fetch( QueryInterface $query )
    {
                 
       return array_map( [$this, $this->callbackCreateEntity ], $this->service->fetch( $this->createNew()->query($query)->getQuerySelect() ) ) ;
       
    }
    
    public function delete(DatabaseEntityInterface $entity)
    {
        
        return $this->service->execute( $entity->query( $this->query() )->getQueryDelete() );
               
    }
    
    
    public function query()
    {
        
        return new SqlQuery( $this ) ;
    }
    
    public function quote( $var )
    {
        return $this->service ->quote($var) ;
    }
    
    
}
