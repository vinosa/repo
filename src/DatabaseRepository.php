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
        parent::__construct($logger) ;                    
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
        try{       
            $statement = $this->selectStatement($query->limit(1));                                  
            $statement->execute();                      
            $row = $statement->fetch() ;           
            $this->logger->debug( $statement->queryString . " (". $statement->rowCount() . " rows)" );                      
            if(!is_array($row) || count($row) == 0 ){               
                throw new ObjectNotFoundException( "EMPTY RESULT: " . $statement->queryString );               
            }                      
            return $this->createNew($row) ;
        } 
        catch (\PDOException $ex) {                        
            throw new RepositoryException( $ex->getMessage()  );
        }               
    }
    
    public function fetch(SqlQuery $query )
    {                                      
        try{          
            $statement = $this->selectStatement( $query );                                 
            $statement->execute();                     
            $rows = $statement->fetchAll() ;          
            $this->logger->debug( $statement->queryString . " (". $statement->rowCount() . " rows)" );           
            return \array_map( [$this,"createNew"], $rows );           
        } 
        catch (\PDOException $ex) {                     
            throw new RepositoryException(  $ex->getMessage()  );          
        }               
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
        $query = (new SqlQuery() )->from($this->table() )->select( $this->fields() ) ;       
        if(is_null($entity)){          
            return $query ;           
        }                                                   
        foreach($this->entityReflection()->getEntityProperties() as  $property ){            
            $key = $property->name() ;           
            $value = $this->getEntityPropertyValue($entity, $key );                    
            if($this->isFieldUnique($key) && !empty($value) ){                
                $query = $query->where($key, $value)  ;
            }         
            if( $property->readOnly() || empty($value) ){                
                continue ;
            }                                                                     
            $query = $query->insert($key, $value) ;                                      
            if( !$this->isFieldUnique($key)){                              
                $query = $query->update($key, $value);
            }           
        }                               
        return $query ;
    }
       
    protected function persistStatement($entity): \PDOStatement
    {
        $query = $this->query( $entity );        
        $toBound = [];       
        foreach($query->getInsert() as $field => $value){                       
            if($this->isFieldUnescaped($entity, $field)){                
                $t[] = $value ;
            }
            else{               
                $t[] = ":" . $field ;
                $toBound[] = $field ;
            }
        }            
        $t2 = [];              
        foreach($query->getUpdate() as $field => $value){            
            if($this->isFieldUnescaped($entity, $field)){                
                $t2[] = "{$field} = {$value}" ;
            }
            else{               
                $t2[] = "{$field} = :" . $field;
            }
        }                      
        $q = "INSERT INTO " . $query->getFrom() . " (" . implode(", " , array_keys( $query->getInsert()) ) . ") VALUES (" . implode(",",$t) . ")" 
            
            . " ON DUPLICATE KEY UPDATE " . implode(",",$t2); 
                 
        $statement = $this->pdo->prepare( $q );       
        foreach($toBound as $field){           
            $statement->bindParam($field, $query->getInsert()[$field]);
        }       
        return $statement;
    }
       
    public function column($column): string
    {        
        return $this->table() . "."  . $column ;
    }
    
    public function table(): string
    {
        return $this->entityReflection()->getTagShortDescription("table") ;
    }
    
    public function fields(): array
    {
        $table = $this->table() ;        
        return \array_map(function($property) use($table) {return $table . "." . $property->name();}, $this->entityReflection()->getEntityProperties() ) ; 
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
    
    protected function isFieldUnescaped($entity,$field)
    {  
        if( in_array($field, $this->entityReflection()->getTagPropertyNames("unescaped") ) ){           
            return true;
        }      
        return false ;
    }
    
    protected function setLastInsertId($entity)
    {
        $primary = $this->entityReflection()->getTagPropertyNames("primary");       
        if(count($primary)>0){            
            $field = $primary[0] ;           
            $id = $this->pdo->lastInsertId() ;           
            $this->logger->debug("last insert id for " . $this->table() . "." . $field . " is " . $id);
            $value = $this->getEntityPropertyValue($entity, $field);           
            if(empty($value)){                
                $this->setEntityPropertyValue($entity, $field, $id ) ;
            }
        }
    }
    
    protected function isFieldUnique($field): bool
    {      
        return in_array( $field, array_merge($this->entityReflection()->getTagPropertyNames("unique"), $this->entityReflection()->getTagPropertyNames("primary") ) );
    } 
    
}
