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
    protected $docComment;
    protected $lines ;
    protected $className ;
    protected $entityProperties = null ;
    protected $reflectionClass ;
    protected $reflectionProperties ;
    
    public function __construct(string $className)
    {
        $this->reflectionClass = new \ReflectionClass($className) ;       
        $this->docComment = $this->reflectionClass->getDocComment();       
        $this->reflectionProperties = $this->reflectionClass->getProperties();            
        $this->className = $className ;
    }
    
    public function getReflectionProperties()
    {
        return $this->reflectionProperties ;
    }
  
    public function getEntityProperties()
    {
        try{
            $block = ( new \Zend_Reflection_Class( $this->className) )->getDocblock();           
            $properties = [] ;           
            foreach(\array_map( [$this, "getTagPropertyName"] , $block->getTags("property") ) as $name){           
                $properties[] = new EntityProperty( $name ) ;
            }            
            foreach(\array_map( [$this, "getTagPropertyName"] , $block->getTags("property-read") ) as $name){       
                $properties[] = new EntityProperty( $name, true ) ;
            } 
            return $properties ;           
        } catch (\Zend_Reflection_Exception $ex) {           
            return [] ;           
        }             
    }
    
    protected function getBlockPropertyTags(\Zend_Reflection_Docblock $block)
    {      
        return $block->getTags("property")  ;
    }
    
    protected function getTagPropertyName(\Zend_Reflection_Docblock_Tag $tag)
    {       
        $exploded = explode(" ", $tag->getDescription() );              
        foreach($exploded as $val){           
            if(strpos($val,"$") === 0){               
                return \substr($val,1);
            }
        }       
        throw new ModelException("no property name declared") ;
    }
    
    protected function getTagByPropertyName(\Zend_Reflection_Docblock $block, $propertyName)
    {
        foreach($this->getBlockPropertyTags($block) as $tag){          
            if($this->getTagPropertyName($tag) == $propertyName){              
                return $tag ;
            }
        }    
        return new \Zend_Reflection_Docblock_Tag("");      
    }    
    
    public function getTagShortDescription(string $tag): string
    {       
        foreach( ( new \Zend_Reflection_Class( $this->className) )->getDocblock()->getTags($tag) as $tag){            
            return $tag->getDescription();
        }       
        throw new ReflectionException("no tag " . $tag .  " in doc comment of class " . $this->className ) ;
    }
    
}
class ReflectionException extends \Exception
{
    
}
