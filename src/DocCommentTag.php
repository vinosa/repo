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
 * Description of DocCommentTag
 *
 * @author vinosa
 */
class DocCommentTag
{
    protected $line;
    protected $name = null ;
    protected $shortDescription = null;
    protected $options = [];
    protected $propertyName = null ;
    private $patternName = "/@[\w-\\\\]+/" ;
    
    public function __construct(string $line)
    {
        $this->line = $line ;
    }
    
    public function getLine()
    {
        return $this->line ;
    }
    
    public function getShortDescription(): string
    {
        if(is_null($this->shortDescription)){
            $line = preg_replace(["/(\*)/" , $this->patternName], '', trim($this->line));  
            preg_match("/[\S]+/", $line, $matches);
            if(count($matches) > 0){
                 $this->shortDescription = $matches[0] ;
            }
            else{
                $this->shortDescription = "" ;
            }
        }
        return $this->shortDescription;
    }
    
    public function getName()
    {
        if(is_null($this->name)){
            preg_match($this->patternName,$this->line,$matches) ;
            if(count($matches) > 0){
                $this->name = substr($matches[0],1) ;
            }
            else{
                $this->name = "";
            }            
        }
        return $this->name ;
    }
    
    public function getOption($option)
    {
        if(!isset($this->options[$option])){
            preg_match('/' . $option . '=["|\']?([^"\'\s\)]*)["|\']?/', $this->line, $matches);
            if(count($matches) < 2 ){
                return null ;
            }
            $this->options[$option] = $matches[1];
        }
        return $this->options[$option] ;
    }
    
    public function requireOption($name)
    {
        $option = $this->getOption($name);
        if(is_null($option)){
            throw new RepositoryReflectionException("no option " . $name . " in tag " . $this->line);
        }
        return $option ;
    }
    
    public function getPropertyName()
    {
        if(is_null($this->propertyName)){
            preg_match('/\$[\w]+/', $this->line, $matches);
            if(count($matches) < 1 ){
                throw new RepositoryReflectionException("no property name in tag " . $this->line);
            }
            $this->propertyName = substr($matches[0],1) ;
        }
        return $this->propertyName ;
    }
}
