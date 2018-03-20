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
namespace Vinosa\Repo\Reflection ;

use Vinosa\Repo\EntityProperty ;
/**
 * Description of CommentLine
 *
 * @author vino
 */
class DocCommentLine
{
    
    private $tokens;
    
    public function __construct($content)
    {
       
        $this->tokens = explode(" ", $content);
    }
    
    public function isProperty()
    {
        return $this->tokenStartingWith("@property") !== false ;
    }
    
    public function isCondition()
    {
        return $this->tokenStartingWith("@condition") !== false ;
    }
    
    public function isTable()
    {
        return $this->tokenStartingWith("@table") !== false ;
    }
    
    public function isKey()
    {
        return $this->tokenStartingWith("@key") !== false ;
    }
    
    public function isCore()
    {
        return $this->tokenStartingWith("@core") !== false ;
    }
    
    public function core()
    {
        
        return $this->nextTokenAfter("@core") ;
               
    }
    
    public function condition()
    {
        
        return $this->nextTokenAfter("@condition") ;
               
    }
    
    public function table()
    {
        
        return $this->nextTokenAfter("@table") ;
               
    }
    
    public function key()
    {
        
        return $this->nextTokenAfter("@key") ;
               
    }
    
    public function propertyName()
    {
        $token = $this->tokenStartingWith("$");
        
        if($token === false){
            
            throw new DocCommentException("no property name");
            
        }
        
        return substr( $token, 1) ;
        
    }
    
    public function isReadonly()
    {
        return $this->tokenStartingWith("@property-read") !== false ;
    }
    
           
    private function tokenStartingWith($str)
    {
        foreach($this->tokens as $token){
            
            if(strpos($token, $str) !== false){
                
                return $token ;
            }
            
        }
        
        return false ;
    }
    
    private function token($position)
    {
        if(count($this->tokens) < ($position + 1) ){
           
           throw new DocCommentException("no token at position " . $position );
           
       }
       
       return $this->tokens[ $position ] ;
    }
    
    private function positionOfTokenStartingWith($str)
    {
        foreach($this->tokens as $position => $token){
            
            if(strpos($token, $str) !== false){
                
                return $position ;
            }
            
        }
        
        throw new DocCommentException("no token starting with " . $str) ;
    }
    
    private function nextTokenAfter( $str )
    {
       
       return $this->token( $this->positionOfTokenStartingWith($str) + 1 ) ;
           
    }
    
}
