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

namespace Vinosa\Repo;

/**
 * Description of DatabaseRepository
 *
 * @author vinosa
 */
class DatabaseRepository extends AbstractRepository
{
    protected $pdo ;
    protected $defaultConditionOperator = "=" ;
    
    public function __construct(\Psr\Log\LoggerInterface $logger, \PDO $pdo)
    {        
        $this->pdo = $pdo ;                                
        $this->logger = $logger ;                      
    } 
          
    public function persist($entity )
    {     
        try{
            $statement = $this->persistStatement( $entity) ;            
            $result = $statement->execute( ) ;           
            $this->logger->debug( $statement->queryString . " (" . $statement->rowCount() . " rows)" );           
            if($statement->rowCount() > 0){
                $this->setLastInsertId($entity) ;
            }                      
            return $entity ;
        } 
        catch (\PDOException $ex) {    
            throw new RepositoryException( $ex->getMessage() );
        }       
    }
       
    public function get(SqlQuery $query)
    {                                           
        $statement = $this->selectStatement($query->limit(1));                                   
        $statement->execute();                       
        $row = $statement->fetch() ;            
        $this->logger->debug( $statement->queryString . " (". $statement->rowCount() . " rows)" );                      
        if($statement->rowCount() === 0){ 
            return null ;                
        }  
        return $this->createNew($row) ;                    
    }
    
    public function getOrFail(SqlQuery $query)
    {
        $statement = $this->selectStatement($query->limit(1));                                   
        $statement->execute();                       
        $row = $statement->fetch() ;            
        $this->logger->debug( $statement->queryString . " (". $statement->rowCount() . " rows)" );                      
        if($statement->rowCount() === 0){                 
            throw new \OE\Core\Exceptions\ObjectNotFoundException( "EMPTY RESULT: " . $statement->queryString );                
        }                      
        return $this->createNew($row) ;
    }
    
    public function fetch(SqlQuery $query )
    {         
        $statement = $this->selectStatement( $query );                                 
        $statement->execute();                       
        $rows = $statement->fetchAll() ;  
        $this->logger->debug( $statement->queryString . " (". $statement->rowCount() . " rows)" );    
        return \array_map( [$this,"createNew"], $rows );                         
    }
           
    public function count(SqlQuery $query)
    {                             
        try{
            $statement = $this->countStatement($query);                                   
            $statement->execute();           
            $this->logger->debug( $statement->queryString );                       
            return $statement->fetchColumn() ;
        } 
        catch (\PDOException $ex) {                       
            throw new RepositoryException( $ex->getMessage() );
        }               
    }
       
    public function query($entity = null): SqlQuery
    {  
        return (new SqlQuery() )->from( $this->table() )->select( $this->fields() ) ; 
    }
       
    protected function persistStatement($entity): \PDOStatement
    {
        $bound = [];$insert = [];$values = [];$update = [];  
        foreach($this->reflection()->getFieldsMapping( \get_class($entity) ) as $columnName => $property){
            $docComment = $this->reflection()->getReflectionPropertyComment( $property ) ;
            $tag = $docComment->getTag("ORM\Column");
            $type = $tag->getOption("type");
            $property->setAccessible(true);
            $value = $property->getValue( $entity );
            if(empty($value)){
                continue ;
            }
            if($type == "datetime"){
                $values[] = $value;
            }
            else{
                $values[] = ":" . $columnName;
                $bound[$columnName] = $value ;
            }
            $insert[] = $columnName;
            if( !$docComment->hasTag("ORM\Id")  && (bool)$tag->getOption("unique") == false ){
                $update[] = $columnName . "=" . end($values) ;
            }                
        }
        $q = "INSERT INTO " . $this->table() . " (" . \implode(", " , $insert ) . ") VALUES (" . \implode(",", $values )  . ")"            
            . " ON DUPLICATE KEY UPDATE " . \implode( ",", $update ); 

        $statement = $this->db->pdo()->prepare( $q );        
        foreach($bound as $key => $value){      
            $statement->bindValue($key, $value);
        }      
        return $statement;        
    }
       
    public function column($column): string
    {        
        return $this->table() . "."  . $column ;
    }
    
    public function table(): string
    {
        return $this->reflection()->getClassComment( $this->entityFullClassname() )->getTag("ORM\Table")->requireOption("name") ;
    }
    
    public function fields(): array
    {
        $fields = [];
        foreach($this->reflection()->getFieldsMapping( $this->entityFullClassname() ) as $field => $property){
            $fields[] = $this->table() .  "." . $field ;       
        }
        return $fields ;
    }
        
    public function countStatement(SqlQuery $query) : \PDOStatement
    {
        $q = "SELECT count(*) FROM " . $query->getFrom() ;         
        $conditionString = $this->conditionsString( $query->getConditions() ) ;         
        if( $conditionString != "" ){           
            $q .= " WHERE " . $conditionString; 
        }        
        $statement = $this->pdo->prepare($q) ;        
        $this->bindConditionsValues($query->getConditions(), $statement);        
        return $statement ;
        
    }
    
    protected function conditionToString(Condition $condition): string
    {       
        $operator = $condition->operator ;        
        if(is_null($operator)){           
            $operator = $this->defaultConditionOperator ;
        }            
        $value = ":" . str_replace(".","", $condition->field) ;        
        if($condition->escape == false){            
            $value = $condition->value ;
        }                            
        return $condition->field . $operator . $value ;              
    }
    
    protected function conditionsString(array $conditions): string
    {      
        $str = "";       
        foreach($conditions as $condition){            
            if(is_string($condition)){                
                $str .= " " . $condition ;
            }           
            if(is_a($condition, Condition::class)){                
                $str .= " " . $this->conditionToString($condition) ;
            }            
            if(is_array($condition)){               
                $str .= " (" . $this->conditionsString($condition) . ")" ;
            }
        }       
        return $str ;
    }
    
    protected function selectStatement(SqlQuery $query): \PDOStatement
    {                                       
        $q = "SELECT " . implode(",", $this->fields() ) . " FROM " . $this->table() . " " . $query->getJoin() ;        
        $conditionString = $this->conditionsString( $query->getConditions() ) ;                
        if( $conditionString != "" ){            
            $q .= " WHERE " . $conditionString; 
        }             
        $q .= " LIMIT " . $query->getStart() . "," . $query->getLimit() ;        
        $statement = $this->pdo->prepare($q) ;       
        $this->bindConditionsValues($query->getConditions(), $statement);      
        return $statement ;
    }
    
    protected function bindConditionsValues(array $conditions, \PDOStatement $statement)
    {    
        foreach($conditions as $condition){           
            if(is_a($condition, Condition::class)){               
                $this->bindConditionValues($condition, $statement);
            }           
            if(is_array($condition)){               
                $this->bindConditionsValues($condition,$statement);
            }
        }     
    }
    
    protected function bindConditionValues(Condition $condition, \PDOStatement $statement)
    {
        if($condition->escape)   {               
            if( trim($condition->value) == "NULL"){           
                $statement->bindParam(":" . str_replace(".","", $condition->field), null, \PDO::PARAM_INT) ;               
            }
            else{          
                $statement->bindParam(":" . str_replace(".","", $condition->field), $condition->value) ;      
            }       
        }       
    }
    
    protected function setLastInsertId($entity)
    {
        foreach($this->reflection()->getFieldsMapping(\get_class($entity)) as $property){
            $docComment = $this->reflection()->getReflectionPropertyComment( $property ) ;
            if($docComment->hasTag("ORM\Id")){
                $property->setAccessible(true); 
                if(empty($property->getValue($entity))){
                    $id = $this->db->pdo()->lastInsertId() ;                    
                    $property->setValue($entity, $id);
                    $this->logger->debug("last insert id for " . $this->column($docComment->getTag("ORM\Column")->requireOption("name")) . " is " . $id );
                }
                return ;
            }
        }
    }
    
}
