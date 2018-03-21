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

use Psr\Log\LoggerInterface ;
use Vinosa\Repo\ObjectNotFoundException ;
use Solarium\Client ;
use Vinosa\Repo\QueryException ;
/**
 * Description of SolariumSolrService
 *
 * @author vinosa
 */
class SolariumSolrService implements SolrServiceInterface
{
    
    private $client = null ;
    private $logger ;
    use \Vinosa\Repo\LoggableTrait ;
    
    public function __construct(Client $client, LoggerInterface $logger = null)
    {
        
        $this->client = $client ;
        
        $this->logger = $logger ;
    }
        
    public function quote( $input )
    {
        $query = $this->getClient()->createSelect();
        
        return $query->getHelper()->escapePhrase($input) ;
    }
    
    public function getHelper()
    {
        return $this->getClient()->createSelect()->getHelper() ;
    }
    
    
    public function fetch(SolrQuery $query)
    {
        $select = $this->getClient()->createSelect();
        
		$select->setQuery( $query->getQuery() );

        $select->setRows( $query->getLimit() );
        
        $select->setFields( $query->getFields() );
       
        try{
            
            $this->client->setDefaultEndpoint( $query->core() ) ;
            
        } catch (QueryException $ex) {

        }
        
        try{
                               
			$resultset = $this->getClient()->select( $select );
            
            $this->debug( "query: " . $query->getQuery() . 
                                " , fields: " . implode("," , $query->getFields()) .
                                " , limit " . $query->getLimit() . 
                                ", " . count($resultset) . 
                                " rows, total: " . $resultset->getNumFound()  
                                ) ;
            
            if( count($resultset) == 0 ){
                
                throw new ObjectNotFoundException("") ;
            }
            
            $array = [];
            
            foreach($resultset as $doc){
                
                $array[] = $doc;
                
            }
            
            return $array ;
            
		}
		catch(\Solarium\Exception\HttpException $ex){
            
			$this->error( $ex->getMessage() ) ;
            
            throw new SolrException( $ex->getMessage() );

		}
    }
    
    private function getClient()
    {
        if(is_null($this->client)){
            
            throw new SolrException("Missing statement: " . get_class($this) . "::init()" ) ;
        }
        
        return $this->client ;
    }
    
}
