<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Vinosa\Repo\Model;

use Vinosa\Repo\QueryBuilders\SqlQueryBuilder ;
use Vinosa\Repo\QueryBuilders\QueryBuilderInterface ;
use Vinosa\Repo\Schema\DbSchema ;
use Vinosa\Repo\Exceptions\EmptyFieldException ;

/**
 * Description of AbstractGenericEntity
 *
 * @author vinosa
 */
class AbstractGenericEntity
{
    
    public function getDbSchema()
    {
              
        return (new DbSchema() )->primary( "id" )
                                 ;
        
    }
    
    protected function querySql(SqlQueryBuilder $query )
    {
        $schema = $this->getDbSchema() ;
        
        if( !is_null( $schema->getTable()  ) ){
            
            $query = $query->insertTo( $schema->getTable() )
                            ->from( $schema->getTable() ) ;
            
        }
                    
        foreach($schema->getWriteableFields() as  $key ){
            
            try{
                $value = $query->quote( $this->__get($key) );
            
                if( $schema->isRelational( $key ) ){
                
                    $value = $this->__get($key) ;
                 
                }
            
                $query->insert["`{$key}`"] = $value ;
                       
                if( !$schema->isPrimaryKey($key) ){
                
                    $query->update["`{$key}`"] =  "`{$key}`=" . $value;
                }
            } 
            catch (EmptyFieldException $ex) {
            }         
                       
        }
               
        foreach($schema->getPrimaryKeys() as $key ){
            try{
                
                $query = $query->where($key, $this->__get( $key ) )  ;
            } 
            catch (EmptyFieldException $ex) {
            }
             
        }
               
        return $query ;
    }
    
    protected function querySolr(SolrQueryBuilder $query)
    {
            
        return $query->select( 
            array(
                ISolrField::TITRE,
                ISolrField::NAKED_TEXTE,
                ISolrField::TEXTE,
                ISolrField::NAKED_TITRE,
                ISolrField::TYPE,
                ISolrField::SITE_NAME,
                ISolrField::PLATFORMID,
                ISolrField::LANGUE,
                ISolrField::ENTITY_IDENTITY,
                ISolrField::SITEID,
                ISolrField::ANCESTRY,
                ISolrField::ID
                  )
            ) ;
        
    }
    
    public function query(QueryBuilderInterface $query)
    {
        if(is_a($query, SqlQueryBuilder::class)){
            
            return $this->querySql( $query );
            
        }
        if(is_a($query, SolrQueryBuilder::class)){
            
            return $this->querySolr( $query );
            
        }
    }
      
    
    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }
    
    public function __get( $name )
    {
        if( isset($this->{$name}) ){
            
            return $this->{$name} ;
        }   
        
        throw new EmptyFieldException ("unset field " . $name . print_r($this,true) ) ;
        
    }
}
