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

namespace Vinosa\Repo\Database;

use Psr\Log\LoggerInterface ;
use Vinosa\Repo\ObjectNotFoundException ;


/**
 * Description of DatabaseService
 *
 * @author vinosa
 */
class PdoDatabaseService implements DatabaseServiceInterface
{
    protected $configuration ;
    private $pdo = null;
    private $logger ;
    use \Vinosa\Repo\LoggableTrait ;
    
    public function __construct(DatabaseConfiguration $configuration, LoggerInterface $logger = null)
    {
        $this->configuration = $configuration ;
        
        $this->logger = $logger ;
    }
    
    public function init()
    {
        $str = "mysql:host=" . $this->configuration->getHost() .
               ";dbname=" . $this->configuration->getDatabaseName() ;
        
        try{
            
            $this->pdo = new \PDO($str, $this->configuration->getUser(), $this->configuration->getPassword() );
            
        } catch (\PDOException $ex) {
            
            throw new DatabaseException( $ex->getMessage() );
        }
        
    }
    
    public function fetch( $sql )
    {
              
        return $this->query( $sql )->fetchAll(\PDO::FETCH_ASSOC) ;
        
    }
       
    public function getDatabaseName()
    {
        return $this->configuration->getDatabaseName() ;
    }
    
    public function execute( $sql )
    {
        try{
           
            $pdoStatement = $this->getPdo()->query( $sql ) ;
            
            $this->loggerDebug( $sql . " (" . $pdoStatement->rowCount() . " rows)" );
                        
            return $pdoStatement ;
            
        } catch (\PDOException $ex) {
            
            $this->loggerError( $ex->getMessage() );
            
            throw new DatabaseException( $ex->getMessage() );
            
        }
    }
    
    public function quote( $str)
    {
        return $this->getPdo()->quote( $str ) ;
    }
    
    private function getPdo()
    {
        if(is_null($this->pdo)){
            
            throw new DatabaseException("Missing statement: " . get_class($this) . "::init()");
        }
        
        return $this->pdo;
    }
    
    private function query( $sql )
    {
        try{
           
            $pdoStatement = $this->getPdo()->query( $sql ) ;
            
            $this->loggerDebug( $sql . " (" . $pdoStatement->rowCount() . " rows)" );
            
            if($pdoStatement->rowCount() == 0){
                
                throw new ObjectNotFoundException( $sql . " returned 0 results" ) ; 
           }
        
            return $pdoStatement ;
            
        } catch (\PDOException $ex) {
            
            $this->loggerError( $ex->getMessage() );
            
            throw new DatabaseException( $ex->getMessage() );
            
        }
        
    }
    
    
}
