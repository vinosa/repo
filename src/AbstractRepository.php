<?php

/*
 * Copyright (C) 2018 vino
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
 * Description of AbstractRepository
 *
 * @author vino
 */
abstract class AbstractRepository
{
    protected $logger ;
    private $reflection ;
    private $entityClassname ;
      
    /**
     * creates new entity from an iterable
     * @param type $data
     * @return
     */   
    public function createNew( $data = [] )
    {
        $class = $this->entityFullClassname();         
        $object = new $class ;
        $mapping = $this->reflection()->getFieldsMapping($class);
        foreach($data as $key => $value){  
            if(isset($mapping[$key])){
                $property = $mapping[$key];
                $property->setAccessible(true);
                $property->setValue($object, $value) ;
            }
        } 
        return $object ;     
    }
    
    public function withEntityType(string $entityClass): AbstractRepository
    {
        $new = clone $this;
        $new->entityClassname = $entityClass;
        return $new ;
    }
    
   
    protected function reflection(): Reflection
    {
        if(empty($this->reflection)){
            $this->reflection = new Reflection();
        }
        return $this->reflection ;
    }
      
    public function entityFullClassname(): string
    { 
        if(empty($this->entityClassname)){
            $className =  $this->reflection()->getClassComment( \get_class($this) )->getTag("entity")->getShortDescription() ;
            if(strpos($className,"\\") === false){
                $className = $this->reflection->getReflectionClass(\get_class($this))->getNamespaceName() . "\\" . $className ;
            }
            $this->entityClassname = $className ;
        }
        return $this->entityClassname ;
   }
    
    
    public function findBy( array $criteria,int $offset = 0,int $limit = 0): array
    {
        $query = $this->query()->withCriteria($criteria) ;
        if($limit > 0){
            $query = $query->start($offset)->limit($limit) ;
        }
        return $this->fetch( $query ) ;
    }
    
    public function findOneBy( array $criteria)
    {  
        return $this->get($this->query()->withCriteria($criteria)) ;
    }
    /**
     * same as findOne() but throws exception if not found
     * @param array $criteria
     * @return type
     */
    public function requireOneBy(array $criteria)
    {    
        return $this->getOrFail($this->query()->withCriteria($criteria)) ;
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
    
}
class RepositoryException extends \Exception
{   
}
class ObjectNotFoundException extends \Exception
{
}
