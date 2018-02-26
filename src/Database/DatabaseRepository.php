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

use Vinosa\Repo\QueryBuilders\QueryInterface ;
use Vinosa\Repo\QueryBuilders\SqlQuery ;
use Vinosa\Repo\RepositoryInterface ;


/**
 * Description of DbRepository
 *
 * @author vinosa
 */
class DatabaseRepository implements RepositoryInterface
{
    
    protected $service ;
    protected $prototype = null ;
    protected $class = DatabaseGenericEntity::class ;
      
    public function __construct(DatabaseServiceInterface $service, $prototype = null )
    {
        
        $this->service = $service;
        
        $this->prototype = $prototype ;
    } 
    
    private function createNew( )
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
    
    private function createNewFromRow( $row )
    {
        $new = $this->createNew( ) ;
        
        foreach($row as $key => $value){
            
            $new->__set($key, $value) ;
        }
        
        return $new ;
    }
    
     
    public function getDatabaseName()
    {
        return $this->service->getDatabaseName() ;
    }
       
    public function save(DatabaseEntityInterface $entity )
    {     
                
        $sql = $entity->query( $this->query() ) 
                            ->getQueryInsert() ;
                                           
            
        return $this->service->execute( $sql );
                                                                              
    }
    
    public function update(DatabaseEntityInterface $entity )
    {
                  
        $sql = $entity->query( $this->query() ) 
                            ->getQueryUpdate() ;
                       
        
        return $this->service ->execute( $sql );
                                 
    }
    
    public function get(QueryInterface $query )
    {
        
        $sql = $this->createNew( )
                       ->query( $query )
                       ->limit(1)
                       ->getQuerySelect() ;
              
                                                                         
        return  $this->createNewFromRow( $this->service->getRow( $sql ) ) ;
                    
    }
    
    public function fetch( QueryInterface $query )
    {
              
        $sql = $this->createNew( )
                     ->query( $query )
                     ->getQuerySelect() ;
              
        
        $rows = $this->service->fetchRows( $sql );
            
        $result = [] ;
            
        foreach($rows as $row){
            
            $result[] = $this->createNewFromRow( $row ) ;
        }
                                
       return $result ;              
    }
    
    public function delete(DatabaseEntityInterface $entity)
    {
        $sql = $entity->query( $this->query() )
                        ->getQueryDelete();
               
        return $this->service->execute( $sql );
               
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
