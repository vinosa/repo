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
        try{
            $result = $this->select( $query->select( $this->fields() )->withCore($this->core() ) );        
            $entities = [];        
            foreach($result as $doc){
                $entities[] = $this->createNew( $doc ) ;
            }
            return $entities ;            
        } catch (\OpenEdition\Pac\Lib\Orm\ObjectNotFoundException $ex) {           
            $this->logger->warn("fetch empty result: " . $ex->getMessage() );      
            return [] ;   
        }       
    }
    
    public function get(SolrQuery $query )
    {
        $result =  $this->select( $query->select( $this->fields() )->withCore($this->core() )->limit(1) ) ;            
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
        $queryString = $this->conditionsString( $query->getConditions() ) ;       
		$select->setQuery( $queryString );
        $select->setRows( $query->getLimit() );       
        $select->setFields( $query->getFields() );       
        if( $query->hasCore() ){            
            $this->client->setDefaultEndpoint( $query->getCore() ) ;            
        }       
        try{                               
			$resultset = $this->client->select( $select );            
            $this->logger->debug( "query: " . $queryString . " , fields: " . implode("," , $query->getFields()) .
                                " , limit " . $query->getLimit() . ", " . count($resultset) . " rows, total: " . $resultset->getNumFound() ) ;           
            if( count($resultset) == 0 ){               
                throw new \OpenEdition\Pac\Lib\Orm\ObjectNotFoundException( $queryString ) ;
            }        
            return $resultset ;
        }
		catch(\Solarium\Exception\HttpException $ex){          
			$this->logger->error( $ex->getMessage() ) ;     
            throw new RepositoryException( $ex->getMessage() );
		}
    }
    
    protected function fields()
    {
        return \array_map(function(\OE\Core\Repository\EntityProperty $property) { return $property->name(); }, $this->entityReflection()->getEntityProperties() ) ;
    }
    
    protected function core()
    {
        return $this->entityReflection()->getTagShortDescription("core") ;
    }
    
    protected function getResult()
    {
        return $this->lastResult ;
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
        return $condition->field . ":" . $safeString ;             
    }
    
}
