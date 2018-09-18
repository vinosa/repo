<?php

/*
 * Copyright (C) 2018
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
 * Description of SolrRepository
 *
 * @author vinosa
 */
class SolrRepository extends AbstractRepository
{
    protected $client ;
    public function __construct(\Psr\Log\LoggerInterface $logger, \Solarium\Client $client)
    {
        $this->client = $client ;
        $this->logger = $logger ;     
    }
               
    public function fetch(SolrQuery $query )
    {
        $result = $this->select( $query );
        if( is_null($result) || $result->getNumFound() == 0 || count($result) == 0 ){               
            return [] ;                
        }
        $entities = [];       
        foreach($result as $doc){
            $entities[] = $this->createNew( $doc ) ;
        }
        return $entities ;                        
    }
    
    public function get(SolrQuery $query )
    {
        $result =  $this->select( $query->limit(1) ) ;
        if( is_null($result) || $result->getNumFound() == 0 || count($result) == 0 ){               
            return null ;                
        }
        foreach($result as $doc){
            return $this->createNew( $doc ) ;           
        }        
    }
    
    public function getOrFail(SolrQuery $query )
    {
        $result =  $this->select( $query->limit(1) ) ;
        if( is_null($result) || $result->getNumFound() == 0 || count($result) == 0 ){               
            throw new ObjectNotFoundException("ressource " . $this->entityFullClassname() . " was not found" );                
        }
        foreach($result as $doc){
            return $this->createNew( $doc ) ;           
        }        
    }
    
    public function query(): SolrQuery
    {
        return new SolrQuery() ;
    }
    
    protected function select(SolrQuery $query)
    {
        $select = $this->client->createSelect();
        $this->client->setDefaultEndpoint( $this->core() ) ; 
        $queryString = $this->conditionsString( $query->getConditions() ) ;
        $select->setQuery( $queryString );
        $timer = new Timer();  
        $select->setRows( $query->getLimit() );       
        $select->setFields( $this->fields() );  
        $resultset = $this->client->select( $select );                      
        $this->logger->debug( $queryString . " ( " . count( $resultset ) . " docs ) " . (string) $timer ) ;                            
        $this->lastResult = $resultset;            
        return $resultset;                  
    }
    
    protected function fields()
    {
        return array_keys($this->reflection()->getFieldsMapping( $this->entityFullClassname() ) );
    }
    
    protected function core()
    {
        return $this->reflection()->getClassComment( $this->entityFullClassname() )->getTag("ORM\Core")->getShortDescription() ;
    }
    
    protected function conditionToString(Condition $condition): string
    {  
        $helper = $this->client->createSelect()->getHelper();       
        if( is_array($condition->value) ){               
                $safeValues = array_map( [ $helper, "escapePhrase" ], $condition->value );               
                $safeString = "(". implode( ",", $safeValues ) . ")" ; 
        }
        else{              
             $safeString = $helper->escapePhrase( $condition->value );             
        } 
        $s = $condition->field . ":" . $safeString ;
        if( trim(strtolower($condition->operator)) === "not" || trim($condition->operator) === "!="){
            $s = "!" . $s ;
        }
        return $s ;              
    }
    
}
