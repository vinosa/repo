<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Vinosa\Repo\Adapters ;

use Vinosa\Repo\Exceptions\ObjectNotFoundException ;
/**
 * Description of PdoAdapter
 *
 * @author vinosa
 */
class PdoAdapter implements AdapterInterface
{
    //put your code here
    protected $pdo ;
    protected $configuration ;

    public function __construct( \PDO $pdo )
    {
      
        $this->pdo = $pdo ;
        
    }
      
    
    public function createEntity($class,  $result)
    {
        $result->setFetchMode(\PDO::FETCH_CLASS, $class) ;
        
        return $result->fetch() ;
        
    }
    
    public function createEntities($class,  $result)
    {
        $result->setFetchMode(\PDO::FETCH_CLASS, $class) ;
        
        return $result->fetchAll() ;
        
    }
    
    public function query($query)
    {
        try{
            
            $result = $this->pdo->query($query) ;
            
            if($result->rowCount() == 0){
                
                throw new ObjectNotFoundException( $query . " returned 0 results" ) ; 
           }
        
            return $result ;
            
        } catch (\PDOException $ex) {
            
            throw new AdapterException( $ex->getMessage() );
            
        }
        
    }
    
    
    public function getPdo()
    {
        return $this->pdo ;
    }
    
    
    public function quote($str)
    {
        return $this->pdo->quote($str) ;
    }
}
