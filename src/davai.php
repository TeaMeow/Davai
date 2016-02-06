<?php
class Davai
{
    /**
     * Routes
     */
     
    private $routes = [];
    
    /**
     * Current URL
     * 
     * Current URL.
     * 
     * @var string
     */
     
    private $url = '';
    
    /**
     * Rules
     * 
     * The rules of the capture groups.
     * 
     * @var array
     */
     
    public $rules = ['*' => '.+?',
                     'i' => '[0-9]++',
                     'a' => '[0-9A-Za-z]++',
                     'h' => '[0-9A-Fa-f]++'];
    
    /**
     * Variables
     * 
     * @var array
     */
     
    private $variables = [];
    
    private $parsedUrl   = [];
    private $parsedPath  = [];
    private $parsedGroup = [];
    
    
    
    
    /**
     * CONSTRUCT
     */
     
    function __construct()
    {
        $this->url = $_SERVER['REQUEST_URI'];
    }
    
    
    
    
    /**
     * Map
     * 
     * @param string      $method
     * @param string      $path
     * @param mixed       $func
     * @param string|null $name
     */
    
    function map($method, $path, $func, $name = null)
    {
        /** Separate the path by the slash */
        $path             = explode('/', $path);
        $this->parsedPath = array_filter(array_map('trim', $path));
        
        /** Separate the current url by the slash */
        $url              = explode('/', $this->url);
        $this->parsedUrl  = array_filter(array_map('trim', $url));
        
        
        $this->groupUrl();
      
        if(!$this->validateRules())
            return false;
        
        $this->analyzeVariables();

        if($name !== null)
            $this->storeRoute($name, $this->parsedGroup);


        if(is_string($func))
        {
            if(strpos($func, '#') !== false)
            {
                $funcGroup = explode('#', $func);
                $className = $funcGroup[0];
                $funcName  = $funcGroup[1];
                
                call_user_func_array([$className, $funcName], $this->variables);
            }
            else
            {
                call_user_func_array($func, $this->variables);
            }
        }
        else
        {
            call_user_func_array($func, $this->variables);
        }
    }
    
    
    function reverse($name, $variables)
    {
        if(!isset($this->routes[$name]))
            return false;
        
        $link = '';
        
        foreach($this->routes[$name]['paths'] as $singlePartial)
        {
            $variableName = $singlePartial['variable'];
            
            if($singlePartial['isPure'])
                $link .= $variableName . '/';
            else
                if(isset($variables[$variableName]))
                    $link .= $variables[$variableName] . '/';
                else
                    break;
                                                          
        }
        
        e($link);
    }
    
    
    function storeRoute($name, $group)
    {
        $paths = [];
        
        foreach($group as $single)
        {
            $variable = $single['isPure'] ? $single['rule'] : $single['variable'];
            
            $paths[] = ['variable' => $variable,
                        'isPure'   => $single['isPure']];
        }
            
        $this->routes[$name] = ['paths' => $paths];
    }
    
    
    
    
    /**
     * Validate Rules
     * 
     */
    
    function validateRules()
    {
        foreach($this->parsedGroup as $singleGroup)
        {
            extract($singleGroup);
            
            if($isPure && $rule == $content)
                continue;
            
            if($isLazy && !$content)
                continue;
            
            $regEx = $this->getRule($rule);
            
            preg_match($regEx, $content, $matched);
           
            if($content != $matched[0])
                return false;
        }
        
        return true;
    }
    
    
    
    
    /**
     * Get Rules
     */
     
    function getRule($ruleName)
    {
        $isSplit = strpos($ruleName, '|') !== false;
        
        if($isSplit)
            return '/^(' . $ruleName . ')$/';
        elseif(isset($this->rules[$ruleName]))
            return '/^(' . $this->rules[$ruleName] . ')$/';
        else
            return '/^(' . $ruleName . ')$/';
    }
    
    
    
    
    /**
     * Analyze Variables
     * 
     */
    
    function analyzeVariables()
    {
        foreach($this->parsedGroup as $singleGroup)
        {
            $variableName = $singleGroup['variable'];
            $content      = $singleGroup['content'];
            
            
            if($variableName)
                array_push($this->variables, $content);
        }
    }
    
    
    
    
    /**
     * Group URL
     */
    
    function groupUrl()
    {
        foreach($this->parsedPath as $index => $singlePath)
        {
            $matchedUrl = isset($this->parsedUrl[$index]) ? $this->parsedUrl[$index] : false;
            $partial  = $this->separatePartial($singlePath, $matchedUrl);
        
                
                
            $this->parsedGroup[] = $partial;
        }
    }
    
    
    
    
    /**
     * Separate Partial
     */
    
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