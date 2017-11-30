<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Vinosa\Repo\QueryBuilders ;

use Vinosa\Repo\RepositoryInterface ;
/**
 * Description of AbstractQueryBuilder
 *
 * @author vinosa
 */
class AbstractQueryBuilder implements QueryBuilderInterface
{
    protected $repository ;
       
    protected $start = 0;
    protected $limit = 10;
    protected $whereClause ;
    protected $fields = array() ;
       
    public function __construct(RepositoryInterface $repository )
    {
        $this->repository = $repository ;
        
        $this->whereClause = new WhereClause( $this ) ;

    }
    
    public function __call($name, $arguments)
    {
        if( strpos( strtolower($name), "where" ) !== false ){
            
            $where = $this->whereClause ;
            
            call_user_func_array(array( $where, $name), $arguments ) ;
            
        }
        
        if($name == "get" || $name == "fetch" || $name == "count"){
            
            $arguments[] = $this ;
            
            return call_user_func_array(array( $this->repository, $name), $arguments ) ;
            
        }
        
        return $this ;
    }
    
    public function getRepository()
    {
        return $this->repository ;
    }
    
    public function select($fields)
    {
        if(is_array($fields)){
            
            $this->fields = $fields;
                
        }
        
        if(is_string($fields)){
            
            $this->fields = array( $fields );
        }
        
        return $this ;
    }
    
    public function getFields()
    {
        return $this->fields ;
    }
    
    public function withFields(array $fields)
    {
             
        $this->fields = $fields ;
        
        return $this ;
    }
    
    
    
    public function start($start)
    {
        $this->start = $start ;
        
        return $this ;
    }
    
    public function offset($offset)
    {
              
        return $this->start($offset) ;
        
    }
    
    public function limit($limit)
    {
        $this->limit = $limit ;
        
        return $this ;
    }
    
    public function getStart()
    {
        return $this->start ;
    }
    
    public function getLimit()
    {
        return $this->limit ;
    }
       
    public function nest( )
    {
        return new WhereClause($this) ;
    }
    
     public function quote( $unsafeString )
    {
        
        return $this->getRepository() ->quote( $unsafeString );
        
    }
    
    public function getClause()
    {
        return $this->whereClause ;
    }
}
