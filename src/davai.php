<?php
class Davai
{
    private $routes = ['get'    => [], 
                       'post'   => [],
                       'put'    => [],
                       'delete' => [],
                       'patch'  => []];
                       
    private $url = '';
    
    public $rules = ['*' => '(.+?)',
                     'i' => '([0-9]++)',
                     'a' => '([0-9A-Za-z]++)',
                     'h' => '([0-9A-Fa-f]++)'];
    
    
    private $variables = [];
    
    private $parsedUrl   = [];
    private $parsedPath  = [];
    private $parsedGroup = [];
    
    function __construct()
    {
        $this->url = $_SERVER['REQUEST_URI'];
    }
    
    
    
    function map($method, $path, $func, $name = null)
    {
        /** Separate the path by the slash */
        $path             = explode('/', $path);
        $this->parsedPath = array_filter(array_map('trim', $path));
        
        /** Separate the current url by the slash */
        $url              = explode('/', $this->url);
        $this->parsedUrl  = array_filter(array_map('trim', $url));
        
        
        $this->groupUrl();
        $this->validateRules();
        $this->analyzeVariables();
        
        
        e($this->parsedPath);
    }
    
    
    
    function validateRules()
    {
        foreach($this->parsedGroup as $singleGroup)
        {
            extract($singleGroup);
            
            if($isPure && $rule == $content)
                continue;
        }
        
        e($this->parsedGroup);
    }
    
    function getRule($ruleName)
    {
        $isSplit = strpos($rulePartial, '|') !== false;
        
        if($isSplit)
        {
            return '^(' + $isSplit ')$';
        }
        else
        {
            
        }
    }
    
    
    
    function analyzeVariables()
    {
        foreach($this->parsedGroup as $singleGroup)
        {
            $variableName = $singleGroup['variable'];
            $content      = $singleGroup['content'];
            
            
            if($variableName)
                $this->variables[$variableName] = $content;
        }
    }
    
    
    function groupUrl()
    {
        foreach($this->parsedPath as $index => $singlePath)
        {
            $matchedUrl = isset($this->parsedUrl[$index]) ? $this->parsedUrl[$index] : false;
            $partial  = $this->separatePartial($singlePath, $matchedUrl);
        
                
                
            $this->parsedGroup[] = $partial;
        }
    }
    
    
    function separatePartial($partial, $matchedContent)
    {
        $isLazy      = substr($partial, -2, 1) === '?';
        $isTag       = substr($partial,  0, 1) === '[';
        
        //if(!$isTag)
        //    return false;
        
        $parsedPartial = explode(':', $partial);
        $rule          = ltrim($parsedPartial[0], '[');
        $variable      = rtrim($parsedPartial[1], $isLazy ? '?]' : ']');
        
        
        return ['rule'     => $rule,
                'variable' => $variable !== '' ? $variable : false,
                'isLazy'   => $isLazy,
                'isPure'   => !$isTag,
                'content'  => $matchedContent];
    }
}
?>