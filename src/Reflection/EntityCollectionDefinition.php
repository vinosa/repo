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

use Vinosa\Repo\ModelException ;
/**
 * Description of EntityCollectionDefinition
 *
 * @author vino
 */
class EntityCollectionDefinition
{
    protected $entity;
    protected $docComment ;
    protected $class ;
    
    public function __construct( $class )
    {
        $this->class = $class ;
        
        $this->docComment = new DocComment( $class ) ;
                            
    }
    
    public function createNewEntity()
    {
        try{
            $class = $this->getEntityClass() ;
            
            $glue = "\\" ;
            
            $exploded = explode($glue, $this->class ) ;
            
            array_pop($exploded);
            
            $namespace = implode($glue, $exploded ) ;
                                    
            $class = $namespace . $glue . $class ;            
            
            return new $class ;
            
        } catch (DocCommentException $ex) {
            
            throw new ModelException("no entity defined in doc comment") ;
        }
    }
    
    public function getEntityClass()
    {
        return $this->docComment->getCollectionEntity() ;
    }
}
