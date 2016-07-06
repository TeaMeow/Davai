<?php
class Davai
{
    /**
     * Stores the data of the routes so we can generate a reversed route from here.
     *
     * @var array
     */

    public $routes = [];

    /**
     * The current URL.
     *
     * @var string
     */

    public $url = '';

    /**
     * The current method.
     *
     * @var string
     */

    public $method = '';

    /**
     * The rules of the capture groups.
     *
     * @var array
     */

    public $rules = ['*' => '.+?',
                     'i' => '[0-9]++',
                     'a' => '[0-9A-Za-z]++',
                     'h' => '[0-9A-Fa-f]++'];

    /**
     * Stores the variables for passing though the functions.
     *
     * @var array
     */

    private $variables = [];

    /**
     * Convert the url string to the array, split by the slash.
     *
     * @var array
     */

    private $parsedUrl   = [];

    /**
     * Same as the $parsedUrl, but the path instead of the current url.
     *
     * @var array
     */

    private $parsedPath  = [];

    /**
     * We combined the parsed path and the parsed url together, and stores them to here, also added some extra informations.
     *
     * @var array
     */

    private $parsedGroup = [];

    private $records = [];

    private $basePath = '';



    /**
     * CONSTRUCT
     */

    function __construct()
    {
        /** Set the current url */
        $this->url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : NULL;

        /** And the method */
        $this->method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : '';
    }




    /**
     * CALL
     *
     * @param string $name   The function name.
     * @param array  $args   The arguments.
     */

    function __call($name, $args)
    {
        switch($name)
        {
            case 'get'   :
            case 'post'  :
            case 'put'   :
            case 'delete':
            case 'patch' :
                $path      = $args[0];
                $func      = $args[1];
                $routeName = isset($args[2]) ? $args[2] : null;

                return $this->map(strtoupper($name), $path, $func, $routeName);
                break;

            case 'recordGet'   :
            case 'recordPost'  :
            case 'recordPut'   :
            case 'recordDelete':
            case 'recordPatch' :
                $recordName = $args[0];
                $path       = $this->records[$recordName];
                $func       = $args[1];
                $name       = substr($name, 6);

                $this->map(strtoupper($name), $path, $func);

                return $this;
                break;
        }
    }




    /**
     * Set Base Path
     *
     *
     *
     * @param
     *
     * @return Davai
     */

    function setBasePath($path)
    {
        $this->basePath = $path;

        return $this;
    }




    /**
     * Record
     *
     */

    function record($records)
    {
        $this->records = $records;


        foreach($records as $name => $path)
        {
            /** Separate the path by the slash */
            $path        = explode('/', $path);
            $parsedPath  = array_filter(array_map('trim', $path));

            /** Separate the current url by the slash */
            $url         = explode('/', strtok($this->url, '?'));
            $parsedUrl   = array_filter(array_map('trim', $url));

            $parsedGroup = $this->groupUrl($parsedPath, $parsedUrl);

            $this->storeRoute($name, $parsedGroup);
        }

        return $this;
    }




    /**
     * The url mapping function, here to capture the urls.
     *
     * @param string      $method   The method, ex: GET, POST, DELETE.
     * @param string      $path     The path of the route.
     * @param mixed       $func     The callback function, can be a anonymous function or a function name, or even 'class@function'.
     * @param string|null $name     The name of the route for reverse routing.
     *
     * @return Davai
     */

    function map($method, $path, $func, $name = null)
    {
        /** Clean the last parsed group */
        $this->parsedGroup = [];

        /** Path with the base path */
        $path = $this->basePath . $path;

        /** Separate the path by the slash */
        $path             = explode('/', $path);
        $this->parsedPath = array_filter(array_map('trim', $path));

        /** Separate the current url by the slash */
        $url              = explode('/', strtok($this->url, '?'));
        $this->parsedUrl  = array_filter(array_map('trim', $url));

        /** Group the path and the url together */
        $this->groupUrl();

        /** Save the route to the routes array for reverse routing when the name is not a null */
        if($name !== null)
            $this->storeRoute($name, $this->parsedGroup);

        /** Validate the rules with the current url */
        if(!$this->validateRules())
            return $this;

        /** Convert the captured url contents to the variables */
        $this->analyzeVariables();

        /** The method must be right, or GGWP */
        if($this->method !== strtoupper($method))
            return $this;



        /** Parse the function name when it's a string not a REAL function */
        if(is_string($func))
        {
            /** If there's a hashtag in it, we split the string by it, get the class name and the function name */
            if(strpos($func, '#') !== false)
            {
                $funcGroup = explode('#', $func);
                $className = $funcGroup[0];
                $funcName  = $funcGroup[1];

                /** Call the callback which inside of the class */
                call_user_func_array([$className, $funcName], $this->variables);
            }
        }
        else
        {
            /** Or just call the callback directly */
            call_user_func_array($func, $this->variables);
        }

        return $this;
    }




    /**
     * Generate the reversed route path.
     *
     * @param string $name        The name of the route.
     * @param array  $variables   The variables, key as the variable name.
     *
     * @return string|bool
     */

    function generate($name, $variables = null)
    {
        if(!isset($this->routes[$name]))
            return false;

        $link = '';

        /** Analyze the paths, and apply the variables, convert them back into a string */
        foreach($this->routes[$name]['paths'] as $singlePartial)
        {
            $variableName = $singlePartial['variable'];

            /** Just use the variable name as the path when it's a "pure" variable */
            if($singlePartial['isPure'])
                $link .= $variableName . '/';
            else
                /** Or get the value for the variable and replace it */
                if(isset($variables[$variableName]))
                    $link .= $variables[$variableName] . '/';
                /** Stop parsing the path if there's no matched value for this variable */
                else
                    break;
        }

        return $link;
    }




    /**
     * Parse the group and stores them to the routes array so we can reverse routing with it.
     *
     * @param string $name    The name of the route.
     * @param array  $group   The parsed group.
     *
     * @return Davai
     */

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

        return $this;
    }




    /**
     * Add a custom rule.
     *
     * @param string $name    The shorthand of the rule.
     * @param string $regEx   The regEx.
     *
     * @return Davai
     */

    function addRule($name, $regEx)
    {
        $this->rules[$name] = $regEx;

        return $this;
    }




    /**
     * Make sure the current url is valid with the rules.
     *
     * @return bool
     */

    function validateRules()
    {
        $index     = -1;
        $length    = count($this->parsedGroup);
        $urlLength = count($this->parsedUrl);

        foreach($this->parsedGroup as $singleGroup)
        {
            $index++;

            extract($singleGroup);

            /** When this is the last partial */
            if($index == $length - 1)

                /** Return false if it's not a lazy partial, and there's no more partials in the url, */
                /** and this last partial is not a pure partial */
                /** ex: "/public/[i:userId]" but "/public/" in the url */
                if(!$isPure && !$isLazy && !isset($this->parsedUrl[$index + 1]))
                    return false;


                //elseif(!$isLazy && isset($this->parsedUrl[$index + 1]) && !$content)
                //    return false;



            /** Return false if it's a pure rule but not the same as the captured content */
            if($isPure && $rule != $content)
            {
                echo 'A';
                return false;
            }


            /** Skip when it's a pure rule and the same as the captured content, */
            if($isPure && $rule == $content)

                /** But return false if this partial is the last one, and there's more partials in the url */
                /** ex: "/hello/" but "/hello/world/" in the url */
                if($index == $length - 1 && $index < $urlLength - 1)
                {
                    echo 'B';
                    return false;
                }

                /** Going to the next partial if there's more partials in the url */
                elseif($index != $urlLength - 1)
                    continue;


            /** Skip it when it's a lazy rule, */
            if($isLazy)
            {

                /** and we captured the empty content, */
                if(!$content)
                {
                    echo 'C';
                    return true;
                }
            }


            /** Use regEx to validate the captured content */
            $regEx = $this->getRule($rule);
            preg_match($regEx, $content, $matched);

            /** We don't need the content If the content is not captured by the regex */
            if(!isset($matched[0]))
            {
                echo 'D';
                echo var_dump($regEx);
                echo "\n";
                echo var_dump($content);
                echo "\n";
                echo var_dump($matched);
                return false;
            }

            /** Return false if the content is not matched with the regEx */
            if($content != $matched[0])
            {
                echo 'E';
                return false;
            }
        }

        return true;
    }




    /**
     * Convert a string to a regEx or get the regEx from the rule list.
     *
     * @param  string $ruleName   The name of the rule.
     *
     * @return string
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
     * Make the captured url contents as a pair with the rules.
     *
     * @return Davai
     */

    function analyzeVariables()
    {
        $this->variables = [];

        foreach($this->parsedGroup as $singleGroup)
        {
            $variableName = $singleGroup['variable'];
            $content      = $singleGroup['content'];


            if($variableName)
                array_push($this->variables, $content);
        }

        return $this;
    }




    /**
     * Separate the rule tags, and match it with the url content, then stores to a the parsed group array.
     *
     * @return Davai
     */

    function groupUrl($parsedPath = null, $parsedUrl = null)
    {
        $isAlone     = $parsedPath != null;
        $parsedPath  = $parsedPath ?: $this->parsedPath;
        $parsedUrl   = $parsedUrl  ?: $this->parsedUrl;
        $parsedGroup = [];




        foreach($parsedPath as $index => $singlePath)
        {
            $matchedUrl = isset($parsedUrl[$index]) ? $parsedUrl[$index] : false;
            $partial    = $this->separatePartial($singlePath, $matchedUrl);

            if($isAlone)
                $parsedGroup[] = $partial;
            else
                $this->parsedGroup[] = $partial;
        }

        return $parsedGroup;
    }




    /**
     * Separate the rule tag, and parse it, also match it with the same level url content then return the whole informations.
     *
     * @param string $partial          The rule tag.
     * @param string $matchedContent   The matched url content.
     *
     * @return array
     */

    function separatePartial($partial, $matchedContent)
    {
        $isLazy      = substr($partial, -2, 1) === '?';
        $isTag       = substr($partial,  0, 1) === '[';

        $parsedPartial = explode(':', $partial);
        $rule          = ltrim($parsedPartial[0], '[');
        $variable      = isset($parsedPartial[1]) ? rtrim($parsedPartial[1], $isLazy ? '?]' : ']') : '';

        return ['rule'     => $rule,
                'variable' => $variable !== '' ? $variable : false,
                'isLazy'   => $isLazy,
                'isPure'   => !$isTag,
                'content'  => $matchedContent];
    }
}
?>