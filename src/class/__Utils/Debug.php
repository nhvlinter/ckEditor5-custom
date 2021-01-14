<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 26-09-19
 * Time: 00:14
 */

namespace salesteck\utils;

class Debug
{
    private const
        DEBUG = false,
        EXPOSE_VARIABLE = false
    ;

    private const
        _file = "file",
        _line = "line",
        _function = "function",
        _class = "class",
        _args = "args"
    ;

    public static function _getDebugString(string $__CLASS__FILE__, string $__FUNCTION__, string $__LINE__){
        $baseName = basename($__CLASS__FILE__);
        $className = str_replace(".php", "", $baseName);
        return "$className:$__FUNCTION__() ($baseName:$__LINE__)";
    }




    /**
     * debug and trace
     * @param bool $debug
     * @param bool $js
     */
    public static function _trace(bool $debug = self::DEBUG, bool $js = true ){

        if($debug === true){
            $js === true ? self::_pushDebug("") : self::_printHtml();
            $backTrace = debug_backtrace();
            foreach ($backTrace as $trace){

                $file = str_replace("\\", "/", $trace[self::_file]);

                $args = $trace[self::_args];

                $textFunction = "".self::_function." : ".$trace[self::_function]."() ";

                if(array_key_exists(self::_class, $trace)){
                    $class = str_replace("\\", "/", $trace[self::_class]);
                    $textFunction .= "in ".self::_class." : ".$class." ";
                }

                $textFunction .= "called in file : $file at line ".$trace[self::_line]." with ".sizeof($args)." argument(s)";
                $js ===  true ? self::_pushDebug($textFunction) : self::_printHtml($textFunction);

                foreach ($args as $argument){

                    $typeOfArg = gettype($argument);
                    $sizeText = "";
                    if( $typeOfArg === gettype([]) ){
                        $sizeText = " size(".sizeof($argument).")";
                    }
                    $textArgument = "- $typeOfArg $sizeText : " . json_encode($argument);
                    $textArgument = str_replace('"', "", $textArgument);
                    $js ===  true ? self::_pushDebug($textArgument) : self::_printHtml($textArgument);
                }
            }
            $js ===  true ? self::_pushDebug("") : self::_printHtml();
        }
    }

    public static function _traceHtml(bool $expose = self::EXPOSE_VARIABLE){
        self::_trace($expose, false);
    }

    public static function _exposeVariableHtml(array $var, bool $expose = self::EXPOSE_VARIABLE){
        self::_exposeVariable($var, $expose, false);
    }


    public static function _exposeVariable(array $var, bool $expose = self::EXPOSE_VARIABLE, bool $js = true){
        if($expose === true){
            $debugSession = Session::_getDebug();
//            $js ===  true  ? self::_pushDebug("") : self::_printHtml();
            foreach ($var as $variableName => $variable){
                if( !array_key_exists($variableName, $debugSession)){
                    $typeOfVar = gettype($variable);
                    $class = $typeOfVar === gettype(new \stdClass()) ?  "class(".str_replace("\\", "/", get_class($variable)).")" : "";
                    $varTextArgument = "variable : $variableName ($typeOfVar) $class " ;
                    $js === true ? self::_pushDebug($varTextArgument) : self::_printHtml($varTextArgument);
                    $js === true ? self::_pushDebug($variable) : self::_printHtml($variable);
                }
            }

            $backTrace = debug_backtrace();
            foreach ($backTrace as $trace){
                $file = array_key_exists(self::_file, $trace) ?  str_replace("\\", "/", $trace[self::_file]) : "";
                $textFunction = "-> ".self::_function." : ".$trace[self::_function]."() ";

                if(array_key_exists(self::_class, $trace)){
                    $class = str_replace("\\", "/", $trace[self::_class]);
                    $textFunction .= "in ".self::_class." : ".$class." ";
                }

                $line = array_key_exists(self::_line, $trace) ?  str_replace("\\", "/", $trace[self::_line]) : "";
                $textFunction .= "called in file : $file at line $line";
                $js === true ? self::_pushDebug($textFunction) : self::_printHtml($textFunction);
            }
//            $js === true ? self::_pushDebug("") : self::_printHtml();
        }
    }

    private static function _printHtml($string = ""){
        $typeOfString = gettype($string);
        if($typeOfString !== gettype("")){
            $string = "<pre>".json_encode($string, JSON_PRETTY_PRINT)."</pre>";
        }
        else{
            $string = "$string</br>";
        }
        echo $string;
    }



    private static function _pushDebug($string){
        self::_pushToSession(json_encode($string));
    }

    private static function _pushToSession(string $string){
        $string = addslashes($string);
        $session = Session::_getDebug();
        if($session === null){
            $array = [$string];
            Session::_setDebug($array);
        }else{
            array_push($session, $string);
            Session::_setDebug($session);
        }
    }

    private static function _printConsoleLog(string $log){
        print "Log.printPhp('$log');";
    }

    public static function _logDebug(){
        $session = Session::_getDebug();
        Session::_setDebug(null);
        Session::_destroyDebug();
        if($session !== null){
            print "<script>";
            print "(function() {";
            if( gettype($session) === gettype([])){
                if(sizeof($session) > 0){
                    foreach ($session as $debugString){
                        self::_printConsoleLog($debugString);
                    }
                    self::_printConsoleLog("");
                }
            }else{
                self::_printConsoleLog($session);
                self::_printConsoleLog("");
            }
            print "})();";
            print "</script>";
        }
    }

    public static function _prettyPrint($var){
        $json = json_encode($var, JSON_PRETTY_PRINT);
        echo "<pre>$json</pre>";
    }


    public static function _processTime(callable $callable){
        if(is_callable($callable)){
            $processTime = CustomDateTime::_getTimeStampMilli();

            $callable();

            $processTime = CustomDateTime::_getTimeStampMilli() - $processTime;
            $processTime = (float) ($processTime/1000);
            $processTime = "processTime : $processTime sec";
            self::_prettyPrint($processTime);

        }
    }


}