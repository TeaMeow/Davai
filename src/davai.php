<?php
class Davai
{
    private $routes = ['get'    => [], 
                       'post'   => [],
                       'put'    => [],
                       'delete' => [],
                       'patch'  => []];
                       
    private $url = '';
    
    public $rules = ['*' => '(.*)',
                     'i' => '([0-9]++)',
                     'a' => '([0-9A-Za-z]++)',
                     'h' => '([0-9A-Fa-f]++)'];
    
    private $tempVariables = [];
    
    function __construct()
    {
        $this->url = $_SERVER['REQUEST_URI'];
    }
    
    function map($method, $url, $func, $name = null)
    {
        $this->parseUrl($url);
    }
    
    function validatePartial($partial, $rule)
    {
        $regEx = $this->rules[$rule];
        $regEx = '/^' . $regEx . '$/';
    
        preg_match($regEx, $partial, $matched);
       
        return $partial == $matched[0];
    }
    
    
    function separatePartial($rulePartial)
    {
        if(strpos($rulePartial, ':') === false)
            return false;
            
        $rulePartial = explode(':', $rulePartial);

    
        $rule        = ltrim($rulePartial[0], '{');
        $variable    = rtrim($rulePartial[1], '}');
        
        return ['rule'     => $rule,
                'variable' => $variable];
    }
    
    
    function parseUrl($url)
    {
        $url = explode('/', $url);
        $url = array_filter(array_map('trim', $url));
        
        $currentUrl = explode('/', $this->url);
        $currentUrl = array_filter(array_map('trim', $currentUrl));
        
        $collection = [];
    
        foreach($currentUrl as $key => $singleUrl)
        {
            if(!isset($url[$key]))
                return false;
                
            $collection[] = [$singleUrl, $url[$key]];
        }
        
        $this->tempVariables = [];
        
        foreach($collection as $singleCollection)
        {
            
            $content = $singleCollection[0];
            $rule    = $singleCollection[1];

            if($content == $rule)
                continue;
            
            $partial = $this->separatePartial($rule);
            
            if(!$partial)
                if(!$this->validatePartial($content, $rule))
                    return false;
                
            if($this->validatePartial($content, $partial['rule']))
                $this->tempVariables[$partial['variable']] = $content;
            else
                return false;
        }
        
        e($this->tempVariables);
    }
    
    
    function get()
    {
        
    }
    
    function post()
    {
        
    }
    
    function patch()
    {
        
    }
    
    function delete()
    {
        
    }
    
    function put()
    {
        
    }
}
?>