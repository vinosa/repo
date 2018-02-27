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

use Vinosa\Repo\LoggerInterface ;
use Vinosa\Repo\ObjectNotFoundException ;
/**
 * Description of SolariumSolrService
 *
 * @author vinosa
 */
class SolariumSolrService implements SolrServiceInterface
{
    private $configuration;
    private $client = null ;
    private $logger ;
    use \Vinosa\Repo\LoggableTrait ;
    
    public function __construct(SolrConfiguration $configuration, LoggerInterface $logger = null)
    {
        $this->configuration = $configuration;
        
        $this->logger = $logger ;
    }
    
    public function init()
    {
        
        $this->client = new \Solarium\Client( $this->configuration->getConfigurationArray() ) ;
        
        //$this->client->setDefaultEndpoint( $this->configuration->getCore() ) ;
        
        //$this->client->setAdapter('Solarium\Core\Client\Adapter\Curl');

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
        
        try{
                               
			$resultset = $this->getClient()->select( $select );
            
            $this->loggerDebug( $query->getQuery() . " , LIMIT " . $query->getLimit() . ", " . count($resultset) . " rows, total: " . $resultset->getNumFound()   ) ;
            
            if( count($resultset) == 0 ){
                
                throw new ObjectNotFoundException("") ;
            }
            
            return $resultset ;
            
		}
		catch(\Solarium\Exception\HttpException $ex){
            
			$this->loggerError( $ex->getMessage() ) ;
            
            throw new SolrException( $ex->getMessage() );

		}
    }
    
    private function getClient()
    {
        if(is_null($this->client)){
            
            throw new SolrException("Solr service was not started!") ;
        }
        
        return $this->client ;
    }
    
    private function logError($message)
    {
        if(!is_null($this->logger)){
            
            $this->logger->error( $message ) ;
            
        }
    }
}
