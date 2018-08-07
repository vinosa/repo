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
      
    public function createNew( $data = [] )
    {
        $class = $this->entityFullClassname();                
        $object = new $class ;                     
        foreach($data as $key => $value){              
            foreach($this->entityReflection()->getReflectionProperties() as $property){                 
                if($property->name == $key){                   
                    $property->setAccessible(true);
                    $property->setValue($object, $value) ;
                }
            }
        }       
        return $object ;     
    }
    
    protected function getEntityPropertyValue($entity, $propertyName)
    {       
        foreach($this->entityReflection()->getReflectionProperties() as $property){           
            if($property->name === $propertyName){                
                $property->setAccessible(true);
                return $property->getValue( $entity );              
            }         
        }
        return null ;
    }
    
    protected function setEntityPropertyValue($entity, $propertyName, $value)
    {
        foreach($this->entityReflection()->getReflectionProperties() as $property){            
            if($property->name === $propertyName){                
                $property->setAccessible(true);
                $property->setValue($entity, $value);               
            }           
        }
    }
    
    protected function entityReflection(): Reflection
    {
        if(empty($this->reflection)){
            $this->reflection = new Reflection( $this->entityFullClassname() );
        }
        return $this->reflection ;
    }
      
    public function entityFullClassname(): string
    { 
        if(empty($this->entityClassname)){            
            $className = ( new Reflection( get_class($this) ) )->getTagShortDescription("entity");       
            if(substr($className,0,1) == "\\"){                
                $this->entityClassname = $className ;
            }
            else{       
                $glue = "\\" ;            
                $exploded = explode($glue, \get_class($this) ) ;           
                array_pop( $exploded );          
                $namespace = implode($glue, $exploded ) ;                                  
                $this->entityClassname = $namespace . $glue . $className ; 
            }
        }
        return $this->entityClassname ;
   }
    
    
    public function findBy( array $criteria,int $offset = 0,int $limit = 0): array
    {
        $query = $this->query();       
        foreach($criteria as $key => $value){           
            $query = $query->where($key, $value) ;
        }     
        if($limit > 0){
            $query = $query->start($offset)->limit($limit) ;
        } 
        return $this->fetch( $query ) ;
    }
    
    public function findOneBy( array $criteria)
    {
        $query = $this->query();      
        foreach($criteria as $key => $value){          
            $query = $query->where($key, $value) ;
        } 
        return $this->get($query) ;
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
class ModelException extends \Exception
{   
}
class ObjectNotFoundException extends \Exception
{
}
