<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Vinosa\Repo ;

/**
 * Description of Reflection
 *
 * @author vinosa
 */
class Reflection
{
   protected $classComments = [];
    protected $propertiesComments = [];
    protected $reflectionProperties = [];
    protected $reflectionClasses = [];
    protected $fieldsMapping = [];
    protected $joinProperties = [];
        
    public function getClassComment(string $class): DocComment
    {
        if(!isset($this->classComments[$class])){
            $this->classComments[$class] = new DocComment( $this->getReflectionClass($class)->getDocComment() );
        }
        return $this->classComments[$class];
    }
    
    public function getReflectionPropertyComment(\ReflectionProperty $property): DocComment
    {
        return $this->getPropertyComment($property->getDeclaringClass()->getName(), $property->getName());
    }
    
    protected function getPropertyComment(string $class,string $propertyName): DocComment
    {
        if(!isset($this->propertiesComments[$class][$propertyName])){           
            $this->propertiesComments[$class][$propertyName] = new DocComment( $this->getReflectionClass($class)->getProperty($propertyName)->getDocComment() );
        }
        return $this->propertiesComments[$class][$propertyName] ;
    }
    
    public function getReflectionProperties(string $class): array
    {        
        if(!isset($this->reflectionProperties[$class])){
            $this->reflectionProperties[$class] = (new \ReflectionClass($class) )->getProperties() ;
        }
        return $this->reflectionProperties[$class] ;
    }
    
    public function getReflectionClass(string $class): \ReflectionClass
    {
        if(!isset($this->reflectionClasses[$class])){
            $this->reflectionClasses[$class] = new \ReflectionClass($class);
        }
        return $this->reflectionClasses[$class] ;
    }
    
    public function getFieldsMapping(string $class): array
    {
        if(!isset($this->fieldsMapping[$class])){
            $this->fieldsMapping[$class] = [];
            foreach($this->getReflectionProperties($class) as $property){
                $docComment = $this->getReflectionPropertyComment($property) ;
                if($docComment->hasTag("ORM\Field")){
                    $this->fieldsMapping[$class][$docComment->getTag("ORM\Field")->requireOption("name")] = $property ;
                }
                if($docComment->hasTag("ORM\Column")){
                    $this->fieldsMapping[$class][$docComment->getTag("ORM\Column")->requireOption("name")] = $property ;
                }
            }
        }
        return $this->fieldsMapping[$class] ;
    }
    
    public function getJoinProperties(string $class): array
    {        
        if(!isset($this->joinProperties[$class])){
            $this->joinProperties[$class] = [];
            foreach($this->getReflectionProperties($class) as $property){
                $docComment = $this->getReflectionPropertyComment($property);
                if($docComment->hasTag("ORM\OneToOne")){
                    $this->joinProperties[$class][] = $property ;
                }
            }
        }
        return $this->joinProperties[$class] ;
    }
}
class RepositoryReflectionException extends \Exception
{    
}
