<?php

/*
 * Copyright (C) 2018 vinogradov
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
 * Description of DatabaseService
 *
 * @author vinogradov
 */
class PdoDatabaseService implements DatabaseServiceInterface
{
    protected $configuration ;
    private $pdo = null;
    private $logger ;
    
    public function __construct(DatabaseConfiguration $configuration, LoggerInterface $logger)
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
    
    public function fetchRows( $sql )
    {
        $result = $this->query( $sql ) ;
        
        $rows  = $result->fetchAll(\PDO::FETCH_ASSOC) ;
        
        return $rows ;
    }
    
    public function getRow( $sql )
    {
        $result = $this->query( $sql ) ;
        
        $row  = $result->fetch(\PDO::FETCH_ASSOC) ;
        
        return $row ;
    }
    
    public function getDatabaseName()
    {
        return $this->configuration->getDatabaseName() ;
    }
    
    public function execute( $sql )
    {
        $this->query($sql) ;
    }
    
    public function quote( $str)
    {
        return $this->getPdo()->quote( $str ) ;
    }
    
    private function getPdo()
    {
        if(is_null($this->pdo)){
            
            throw new DatabaseException("PDO was not started");
        }
        
        return $this->pdo;
    }
    
    private function query( $sql )
    {
        try{
            
            $result = $this->getPdo()->query( $sql ) ;
            
            $this->logger->debug( $sql ) ;
            
            if($result->rowCount() == 0){
                
                throw new ObjectNotFoundException( $query . " returned 0 results" ) ; 
           }
        
            return $result ;
            
        } catch (\PDOException $ex) {
            
            $this->logger->error( $ex->getMessage() );
            
            throw new DatabaseException( $ex->getMessage() );
            
        }
        
    }
}
