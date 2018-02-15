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
namespace Vinosa\Repo ;


use Vinosa\Repo\Model\EntityInterface ;
use Vinosa\Repo\QueryBuilders\QueryBuilderInterface ;
use Vinosa\Repo\QueryBuilders\SqlQueryBuilder ;


/**
 * Description of DbRepository
 *
 * @author vinosa
 */
class DatabaseRepository implements RepositoryInterface
{
    
    protected $service ;
    protected $prototype ;
      
    public function __construct(DatabaseServiceInterface $service, EntityInterface $prototype )
    {
        
        $this->service = $service;
        
        $this->prototype = $prototype ;
    } 
    
    private function createNew( )
    {
                  
       $new = clone $this->prototype ;
       
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
       
    public function save(EntityInterface $entity )
    {     
                
        $sql = $entity->query( $this->query() ) 
                            ->getQueryInsert() ;
                                           
            
        $result = $this->service->execute( $sql );
                                                           
                   
    }
    
    public function update(EntityInterface $entity )
    {
                  
        $sql = $entity->query( $this->query() ) 
                            ->getQueryUpdate() ;
                       
        
        $result = $this->service ->execute( $sql );
                       
           
    }
    
    public function get(QueryBuilderInterface $query )
    {
        
        $sql = $this->createNew( )
                       ->query( $query )
                       ->limit(1)
                       ->getQuerySelect() ;
              
                   
        $row = $this->service->getRow( $sql );
            
                                            
        return  $this->createNewFromRow( $row ) ;
                    
    }
    
    public function fetch( QueryBuilderInterface $query )
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
    
    public function delete(EntityInterface $entity)
    {
        $sql = $entity->query( $this->query() )
                        ->getQueryDelete();
               
        $result = $this->service->execute( $sql );
        
        
    }
    
    
    public function query()
    {
        
        return new SqlQueryBuilder( $this ) ;
    }
    
    public function quote( $var )
    {
        return $this->service ->quote($var) ;
    }
    
    
}
