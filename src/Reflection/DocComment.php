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

namespace Vinosa\Repo\Reflection;

/**
 * Description of DocComments
 *
 * @author vino
 */
class DocComment
{
    protected $lines = [];
    protected $class ;
    const LINE_SEPARATOR = "\n" ;
    
    public function __construct($class)
    {
        $this->class = $class ;
        
        $this->lines = array_map( function($line) {return new DocCommentLine( $line) ; },
                            explode(static::LINE_SEPARATOR, (new \ReflectionClass($class) )->getDocComment() )
                          );
    }
    
    public function getEntityProperies()
    {
        $properties = [] ;
    
        foreach($this->lines as $line){
            
            try{
                
                $properties[] = (new EntityProperty() )->withDocCommentLine( $line ) ;
                
            } catch (DocCommentException $ex) {

            }
        }
        
        return $properties ;
    }
    
    public function getEntityConditions()
    {
        $conditions = [];
        
        foreach($this->lines as $line){
                        
            if ( $line->isCondition() ){
                
                $conditions[] = $line->condition() ;
            }
        }
        
        return $conditions ;
    }
    
    public function getEntityTable()
    {
        foreach($this->lines as $line){
                        
            if ( $line->isTable() ){
                
                return $line->table() ;
            }
        }
        
        throw new DocCommentException("no table for entity") ;
    }
    
    public function getEntityCore()
    {
        foreach($this->lines as $line){
                        
            if ( $line->isCore() ){
                
                return $line->core() ;
            }
        }
        
    }
    
    public function getEntityKeys()
    {
        $keys = [];
        
        foreach($this->lines as $line){
                        
            if ( $line->isKey() ){
                
                $keys[] = $line->key() ;
            }
        }
        
        return $keys ;
    }
    
    public function getCollectionEntity()
    {
        foreach($this->lines as $line){
                        
            if ( $line->isEntity() ){
                
                return $line->entity() ;
            }
        }
        
        throw new DocCommentException("no entity defined for collection") ;
    }
}
