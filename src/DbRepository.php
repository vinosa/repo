<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Vinosa\Repo ;

use Vinosa\Repo\Adapters\AdapterInterface ;
use Vinosa\Repo\Exceptions\AdapterException ;
use Vinosa\Repo\Model\EntityInterface ;
use Vinosa\Repo\QueryBuilders\QueryBuilderInterface ;
use Vinosa\Repo\QueryBuilders\SqlQueryBuilder ;
use Vinosa\Repo\Tools\LoggerInterface ;
/**
 * Description of DbRepository
 *
 * @author vinosa
 */
class DbRepository implements RepositoryInterface
{
    
    protected $adapter ;
   
    protected $logger ;
    
    protected $database ;
    
    public function __construct(AdapterInterface $adapter, $database = null, LoggerInterface $logger = null )
    {
        
        $this->adapter = $adapter;
                
        $this->logger = $logger ;
        
        $this->database = $database ;
        
    } 
          
    public function getAdapter()
    {
        return $this->adapter ;       
        
    }
    
    public function getDatabaseName()
    {
        if(!is_null($this->database) ){
            
            return $this->database ;
            
        }
        
        return "" ;
    }
       
    public function save(EntityInterface $entity )
    {     
        try{
                    
            $query = $entity->query( $this->query() ) 
                            ->getQueryInsert() ;
                                    
            $this->startTimer() ;
            
            $result = $this->getAdapter() ->query( $query );
                        
            $this->logQuery( $query ) ;                                   
            
        } 
        catch (AdapterException $ex) {
            
            $this->logQuery( $query ) ;
            
            throw new RepositoryException("saving ". get_class($entity) . " failed: "  . $ex->getMessage() . " " . $query );
        }       
    }
    
    public function update(EntityInterface $entity )
    {
        try{
                     
            $query = $entity->query( $this->query() ) 
                            ->getQueryUpdate() ;
                       
            $this->startTimer() ;
            
            $result = $this->getAdapter() ->query( $query );
            
            $this->logQuery( $query ) ;
            
        } 
        catch (AdapterException $ex) {
            
            $this->logQuery( $query ) ;
            
            throw new RepositoryException(" update " . get_class($entity) .  $ex->getMessage() . " " . $query );
        }       
    }
    
    public function get( $class, QueryBuilderInterface $builder )
    {
        
        $query = $this->create( $class )
                       ->query( $builder )
                       ->limit(1)
                       ->getQuerySelect() ;
              
        try{
            $this->startTimer() ;
            
            $result = $this->getAdapter() ->query( $query );
            
            $this->logQuery( $query ) ;
                                  
            return  $this->adapter->createEntity( $class, $result ) ;
        } 
        catch (AdapterException $ex) {
            
            $this->logQuery( $query ) ;
            
            throw new RepositoryException(" get " . $class .  $ex->getMessage() . " " . $query );
        }               
    }
    
    public function fetch( $class, QueryBuilderInterface $builder )
    {
              
       $query = $this->create( $class)
                     ->query( $builder )
                     ->getQuerySelect() ;
              
        try{
            
            $this->startTimer() ;
            
            $result = $this->getAdapter() ->query( $query );
            
            $this->logQuery( $query ) ;
            
            return $this->adapter->createEntities($class, $result) ;
                                
        } 
        catch (AdapterException $ex) {
            
            $this->logQuery( $query ) ;
            
            throw new RepositoryException( " fetch " . $class . $ex->getMessage() . " " . $query );
            
        }               
    }
    
    public function delete(EntityInterface $entity)
    {
        $query = $entity->query( $this->query() )
                        ->getQueryDelete();
               
        try{
            $this->startTimer() ;
            
            $result = $this->getAdapter() ->query( $query );
            
            $this->logQuery( $query ) ;
                       
            return true ;
        } 
        catch (AdapterException $ex) {
            
            throw new RepositoryException( " delete " . get_class($entity) . $ex->getMessage() . " " . $query );
        }         
        
        
    }
    
    public function count( $class, QueryBuilderInterface $builder )
    {
              
       $query = $this->create( $class )
                     ->query( $builder )
                     ->getQueryCount() ;
            
        try{
            $this->startTimer() ;
            
            $result = $this->getAdapter() ->query( $query );
            
            $this->logQuery( $query ) ;
            
            return $result->fetchColumn() ;

        } 
        catch (AdapterException $ex) {
            
            $this->logQuery( $query ) ;
            
            throw new RepositoryException( $ex->getMessage() . " " . $query );
        }
        
    }
    
    public function query()
    {
        
        return new SqlQueryBuilder( $this, $this->getDatabaseName() ) ;
    }
    
    public function quote( $var )
    {
        return $this->getAdapter() ->quote($var) ;
    }
    
    private function logQuery( $query )
    {
        if(is_null($this->logger) ){
            
            return ;
            
        }
        
        $this->logger->debug( get_class($this) . " : " .$query . " " . $this->logger->getDuration() ) ;
    }
    
    private function startTimer()
    {
        if(is_null($this->logger) ){
            
            return ;
            
        }
        $this->logger->startTimer() ;
    }
    
    private function create( $class )
    {
        return new $class ;
    }
}
