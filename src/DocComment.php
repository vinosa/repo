<?php


namespace Vinosa\Repo ;

class DocComment
{
    protected $comment;
    protected $lines = null ;
    protected $tags = [];
    protected $tagsByName = [];
    
    public function __construct(string $comment)
    {
        $this->comment = $comment; 
    }
    
    public function getTags($name): array
    {
        if(isset($this->tagsByName[$name])){
            return $this->tagsByName[$name] ;
        }
        if(count($this->tags) == 0){            
            foreach($this->lines() as $line){    
                $this->tags[] = new DocCommentTag($line);               
            }
        }
        $this->tagsByName[$name] = [];
        foreach($this->tags as $tag){
            if($tag->getName() === $name){
                $this->tagsByName[$name][] = $tag;
            }
        }
        return $this->tagsByName[$name] ;
    }
    
    public function getTag($name): DocCommentTag
    {
        $tags = $this->getTags($name);
        if(count($tags) == 0){
            throw new RepositoryReflectionException("no tag " . $name . " in DocComment " . $this->comment);
        }
        return $tags[0] ;
    }
    
    public function hasTag(string $tag): bool
    {
        return count($this->getTags($tag) ) > 0 ;
    }
    
    protected function lines(): array
    {
       if(is_null($this->lines)){
           $this->lines = explode("\n", $this->comment );  
       }
       return $this->lines ;
    }
    
}

