<?php

    /*
     * @author BreathLess
     * @description: F Class
     * @package Codeine Framework
     * @subpackage Core
          */

    define('Codeine', __DIR__);
    define ('REQID', microtime(true).rand());

    final class F
    {
        private static $_Environment = 'Production';

        private static $_Options;
        private static $_Code;

        private static $_Service = 'Codeine';
        private static $_Method = 'Do';

        private static $_Storage = [];
        private static $_Ticks = [];
        private static $_Counters = [];
        private static $_Log = [];

        private static $_Live = false;
        private static $_Memory= 0;

        private static $_SR71 = false;

        public static function Environment()
        {
            return self::$_Environment;
        }

        public static function Bootstrap ($Call = null)
        {
            self::$_Live = true;

            if (isset($_REQUEST['SR71']))
                self::$_SR71 = true;

            self::Start(self::$_Service . '.' . self::$_Method);

            mb_internal_encoding('UTF-8');

            setlocale(LC_ALL, "ru_RU.UTF-8");

            if (isset($_SERVER['Environment']))
                self::$_Environment = $_SERVER['Environment'];

            if (isset($Call['Environment']))
                self::$_Environment = $Call['Environment'];

            if (isset($Call['Path']))
            {
                if ((array) $Call['Path'] === $Call['Path'])
                    self::$_Options['Path'] = array_merge($Call['Path'], [Codeine]);
                else
                    self::$_Options['Path'] = [$Call['Path'], Codeine];
            }
            else
                self::$_Options['Path'] = [Codeine];

            if (isset($_COOKIE['Experiment']))
                if (isset(self::$_Options['Experiments'][$_COOKIE['Experiment']]))
                    self::$_Options['Path'][] = Root.'/Labs/'.self::$_Options['Experiments'][$_COOKIE['Experiment']];

            self::loadOptions();

            self::Versioning();

            set_error_handler ('F::Error');

            register_shutdown_function('F::Shutdown');
        }

        protected static function Versioning()
        {
            self::loadOptions('Version');

            if (isset(self::$_Options['Project']['Version']['Codeine'])
                && self::$_Options['Project']['Version']['Codeine'] > self::$_Options['Version']['Codeine']['Major'])
                trigger_error('Codeine '.self::$_Options['Project']['Version']['Codeine'].'+ needed. Installed: '
                    .self::$_Options['Version']['Codeine']['Major']);

            self::Log('Codeine: '.self::$_Options['Version']['Codeine']['Major'], LOG_INFO);
            self::Log('Build: '.self::$_Options['Version']['Codeine']['Minor'], LOG_INFO);

            if (isset(self::$_Options['Version']['Project']))
            {
                self::Log('Project: '.self::$_Options['Version']['Project']['Major'], LOG_INFO);
                self::Log('Build: '.self::$_Options['Version']['Project']['Minor'], LOG_INFO);
            }

            self::Log('Environment: '.self::$_Environment, LOG_INFO);
        }

        public static function Merge($First, $Second)
        {
            if ((array) $Second === $Second)
            {
                if ($First !== $Second)
                {
                    if ((array) $First === $First)
                    {
                        foreach ($Second as $Key => &$Value)
                            if (isset($First[$Key]) && ((array)$Value === $Value))
                                $First[$Key] = self::Merge($First[$Key], $Second[$Key]);
                            else
                                $First[$Key] = $Value;

                    }
                    else
                        $First = $Second;
                }
            }

            return $First;
        }

        public static function Diff ($First, $Second)
        {
            foreach($First as $Key => $Value)
            {
                if ($Value != '*')
                {
                    if(is_array($Value))
                    {
                          if(!isset($Second[$Key]))
                              $Diff[$Key] = $Value;
                          elseif(!is_array($Second[$Key]))
                              $Diff[$Key] = $Value;
                          else
                          {
                              $NewDiff = F::Diff ($Value, $Second[$Key]);

                              if(null !== $NewDiff)
                                    $Diff[$Key] = $NewDiff;
                          }
                      }
                      elseif (!isset($Second[$Key]) || $Second[$Key] != $Value)
                          $Diff[$Key] = $Value;
                }
            }

            return (!isset($Diff) ? null : $Diff);
        }

        public static function findFile($Names)
        {
           $Names = (array) $Names;

           foreach ($Names as $Name)
               foreach (self::$_Options['Path'] as $ic => $Path)
                   if (F::file_exists($Filenames[$ic] = $Path.'/'.$Name))
                        return $Filenames[$ic];

           return null;
        }

        public static function findFiles ($Names)
        {
            $Results = [];

            $Names = (array) $Names;

            foreach (self::$_Options['Path'] as $ic => $Path)
                foreach ($Names as $Name)
                    if (F::file_exists($Filenames[$ic] = $Path . '/' . $Name))
                        $Results[] = $Filenames[$ic];

            $Results = array_reverse($Results);

            if (empty($Results))
                return null;
            else
                return $Results;
        }

        private static function _loadSource($Service)
        {
            $Path = strtr($Service, '.', '/');

            $Filenames = self::findFiles(self::$_Options['Codeine']['Driver']['Path'].'/'.$Path.self::$_Options['Codeine']['Driver']['Extension']);

            if (!empty($Filenames))
            {
                foreach ($Filenames as $Filename)
                    include $Filename;

                return true;
            }
            else
            {
                F::Log($Service.' not found', LOG_CRIT);
                return false;
            }
        }

        /**
         * @description Проверяет, является ли массив правильно сконструированным вызовом.
         * @param  $Call
         * @return bool
         */
        public static function isCall($Call)
        {
            return (((array) $Call === $Call) && isset($Call['Service']) && isset($Call['Method']));
        }

        public static function hashCall($Call)
        {
            if (self::isCall($Call))
                return sha1(serialize($Call));
            else
                return serialize($Call);
        }

        public static function Run($Service, $Method = null , $Call = [])
        {
            if (($sz = func_num_args())>3)
            {
                for($ic = 3; $ic<$sz; $ic++)
                    if (is_array($Argument = func_get_arg ($ic)))
                        $Call = F::Merge($Call, $Argument);
            }

            //F::Log('Run: '.$Service,':'.$Method, 7);
            $Result = F::Execute($Service, $Method, $Call);

            return $Result;
        }

        public static function Execute($Service, $Method, $Call)
        {
            $OldService = self::$_Service;
            $OldMethod = self::$_Method;

            if ($Service !== null)
                self::$_Service = $Service;

            if ($Method !== null)
                self::$_Method  = $Method;

/*            if (($OldService == $Service) && ($OldMethod == $Method))
                self::$_Overflow++;
            else
                self::$_Overflow = 0;*/


            $FnOptions = self::loadOptions();

            if(!isset($FnOptions['Isolated']))
            {
                $Call = self::Merge($FnOptions, $Call);
                $FnOptions = [];
            }

            if ((null === self::getFn(self::$_Method)) && !self::_loadSource(self::$_Service))
                $Result = (is_array($Call) && isset($Call['Fallback']))? $Call['Fallback'] : null;
            else
            {
                $F = self::getFn(self::$_Method);

                if (is_callable($F))
                {
                    if (self::$_SR71)
                    {
                        self::Stop($OldService. '.' . $OldMethod);
                        self::Start(self::$_Service . '.' . self::$_Method);
                    }

                    $Result = $F(F::Merge($Call, $FnOptions));

                    if (self::$_SR71)
                    {
                        self::$_Memory = memory_get_usage(true);
                        self::Counter(self::$_Service.'.'.self::$_Method);
                        self::Stop(self::$_Service . '.' . self::$_Method);
                        self::Start($OldService. '.' . $OldMethod);
                    }
                }
                else
                    $Result = isset($Call['Fallback']) ? $Call['Fallback'] : null;

            }

            self::$_Service = $OldService;
            self::$_Method = $OldMethod;

            return $Result;
        }

        public static function setFn($Function, $Code = null)
        {
            return self::$_Code[self::$_Service][$Function] = $Code;
        }

        public static function getFn($Function)
        {
            if (isset(self::$_Code[self::$_Service][$Function]))
            {
                return self::$_Code[self::$_Service][$Function];
            }
            else
                return null;
        }

        public static function Live($Variable, $Call = [])
        {
            if ($Variable instanceof Closure)
                return $Variable($Call);

            if (isset($Variable['NoLive']))
                return $Variable;

            if (self::isCall($Variable))
            {
                if (($sz = func_num_args())>2)
                {
                    for($ic = 2; $ic<$sz; $ic++)
                        if (is_array($Argument = func_get_arg ($ic)))
                            $Call = F::Merge($Call, $Argument);
                }

                return F::Run($Variable['Service'], $Variable['Method'],
                    $Call, isset($Variable['Call'])? $Variable['Call']: []);
            }
            else
            {
                if ((array) $Variable === $Variable)
                    foreach ($Variable as $Key => &$cVariable)
                        $Variable = F::Dot($Variable, $Key, self::Live($cVariable, $Call));
                else
                    if ('$' == substr($Variable, 0, 1))
                        $Variable = F::Dot($Call, substr($Variable, 1));

                return $Variable;
            }
        }

        public static function Extract($Array, $Keys)
        {
            $Data = [];

            if (is_array($Array))
                foreach ($Array as $IX => $Row)
                    foreach ($Keys as $Key)
                        if (is_scalar($Key) && isset($Row[$Key]))
                            $Data[$Key][$IX] = $Row[$Key];

            return $Data;
        }

        public static function Sort($Array, $Key, $Direction = SORT_DESC)
        {
            $Data = [];

            foreach ($Array as $Row)
                if (isset($Row[$Key]))
                    $Data[$Row['ID']] = $Row[$Key];

            $Direction == SORT_ASC? asort($Data): arsort($Data);

            return $Data;
        }

        public static function Map ($Array, $Fn, $Data = null, $FullKey = '')
        {
            if (is_array ($Array))
                foreach ($Array as $Key => &$Value)
                {
                    $NewFullKey = is_numeric($Key)? $FullKey.'#': $FullKey.'.'.$Key;

                    $Fn($Key, $Value, $Data, $NewFullKey, $Array);

                    if (is_array ($Value))
                        $Array[$Key] = self::Map ($Value, $Fn, $Data, $NewFullKey, $Array);
                    else
                        $Array[$Key] = $Value;
                }
            else
                $Array = $Fn('', $Array, $Data, $FullKey);

            return $Array;
        }

        public static function Dot ($Array, $Key)
        {
            if (func_num_args() == 3)
            {
                $Value = func_get_arg(2);

                if (is_array($Array))
                {
                    if (is_numeric($Key))
                        $Key = (int) $Key;

                    if (strpos($Key, '.') !== false)
                    {
                        $Keys = explode('.', $Key);
                        $Key = array_shift($Keys);

                        if (!isset($Array[$Key]))
                            $Array[$Key] = [];

                        $Array[$Key] = F::Dot($Array[$Key], implode('.', $Keys), $Value);
                    }
                    else
                    {
                        if ($Value === null)
                            unset($Array[$Key]);
                        else
                            $Array[$Key] = $Value;
                    }
                }

                return $Array;
            }

            if (strpos($Key, '.') !== false)
            {
                $Keys = explode('.', $Key);

                $Tail = $Array;

                foreach ($Keys as $iKey)
                    if (isset($Tail[$iKey]))
                        $Tail = $Tail[$iKey];
                    else
                    {
                        if (isset($Array[$Key]))
                            return $Array[$Key];
                        else
                            return null;
                    }

                return $Tail;
            }
            else
                return isset($Array[$Key])? $Array[$Key]: null;
        }

        public static function Hook($On, $Call)
        {
             if (isset($Call['Custom Hooks'][$On]))
                 $On = $Call['Custom Hooks'][$On];

             if (isset($Call['Hooks']) && ($Hooks = F::Dot($Call, 'Hooks.' . $On)) && (!isset($Call['No'][$On])))
             {
                 foreach ($Hooks as $HookName => $Hook)
                 {
                     F::Log($On.':'.$HookName, LOG_DEBUG);
                     if (F::isCall($Hook))
                         $Call = F::Run($Hook['Service'],$Hook['Method'], isset($Hook['Call'])? $Hook['Call']: [], $Call, ['On' => $On]);
                     else
                         $Call = F::Merge($Call, $Hook);
                 }
             }

            return $Call;
        }

        public static function Error($errno , $errstr , $errfile , $errline , $errcontext)
        {
            return F::Log($errno.' '.$errstr.' '.$errfile.'@'.$errline, LOG_ERR);
        }

        /*
         * Verbosity
         *
         *
         * 7 - Debug
         * 6 - Notice
         * 5 - Warning
         * 4 - Error
         * 3 - Failure
         * 2 - Critical
         * 1 - Emergency
         * 0 - Apocalypse
         */

        public static function Log ($Message, $Verbose = 7)
        {
            if ($Verbose <= self::$_Options['Codeine']['Verbose'])
                return self::$_Log[] = [$Verbose, round(microtime(true) - self::$_Ticks['T']['Codeine.Do'], 4), $Message, self::$_Service];
        }

        public static function Logs()
        {
            return self::$_Log;
        }

        public static function loadOptions($Service = null, $Method = null)
        {
            $Service = ($Service == null)? self::$_Service: $Service;
            $Method = ($Method == null)? self::$_Method: $Method;

            // Если контракт уже не загружен
            if (!isset(self::$_Options[$Service]))
            {
                $Options = [];

                $ServicePath = strtr($Service, '.', '/');

                $Filenames = [];

                if (self::$_Environment != 'Production')
                    $Filenames[] = 'Options/'.$ServicePath.'.'.self::$_Environment.'.json';

                $Filenames[] = 'Options/'.$ServicePath.'.json';

                if ($Filenames = self::findFiles ($Filenames))
                {
                    foreach ($Filenames as $Filename)
                    {
                        $Current = json_decode(file_get_contents($Filename), true);

                        if ($Filename && !$Current)
                        {
                            switch (json_last_error()) {
                                case JSON_ERROR_NONE:
                                    $JSONError =  ' - No errors';
                                break;
                                case JSON_ERROR_DEPTH:
                                    $JSONError =  ' - Maximum stack depth exceeded';
                                break;
                                case JSON_ERROR_STATE_MISMATCH:
                                    $JSONError =  ' - Underflow or the modes mismatch';
                                break;
                                case JSON_ERROR_CTRL_CHAR:
                                    $JSONError =  ' - Unexpected control character found';
                                break;
                                case JSON_ERROR_SYNTAX:
                                    $JSONError =  ' - Syntax error, malformed JSON';
                                break;
                                case JSON_ERROR_UTF8:
                                    $JSONError =  ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                                break;
                                default:
                                    $JSONError =  ' - Unknown error';
                                break;
                            }

                            trigger_error('JSON Error: ' . $Filename.':'. $JSONError); //FIXME
                            return null;
                        }

                        $Options = self::Merge($Options, $Current);
                    }
                }

                if (isset($Options['Mixins']))
                {
                    foreach($Options['Mixins'] as &$Mixin)
                        $Options = F::Merge(F::loadOptions($Mixin), $Options);

                    unset($Options['Mixins']);
                }

                self::$_Options[$Service] = $Options;
            }

            return self::$_Options[$Service];
        }

        public static function Dump($File, $Line, $Call)
        {
            echo '<div class="console">'.substr($File, strpos($File, 'Drivers')).'@'.$Line.'<br/><code>'.file($File)[$Line-1].'</code>';

            if (is_array($Call))
            {
                $Call2 = $Call;
                ksort($Call2);
                var_dump($Call2);
            }
            else
                var_dump($Call);

            echo '</div>';
            return $Call;
        }

        public static function Set ($Key, $Value)
        {
            return self::$_Storage[$Key] = $Value;
        }

        public static function Get ($Key)
        {
            return (isset(self::$_Storage[$Key]) ? self::$_Storage[$Key]: null);
        }

        public static function file_exists($Filename)
        {
            return isset(self::$_Storage['FE'][$Filename])? self::$_Storage['FE'][$Filename]: self::$_Storage['FE'][$Filename] = file_exists($Filename);
        }

        public static function Counter ($Key, $Value = 1)
        {
            if (isset(self::$_Counters['C'][$Key]))
                self::$_Counters['C'][$Key]+= $Value;
            else
                self::$_Counters['C'][$Key] = $Value;

            return self::$_Counters['C'][$Key];
        }

        public static function Start ($Key)
        {
            return self::$_Ticks['T'][$Key] = microtime(true);
        }

        public static function Stop ($Key)
        {
            if (isset(self::$_Counters['T'][$Key]))
                return self::$_Counters['T'][$Key] += round((microtime(true) - self::$_Ticks['T'][$Key])*1000,2);
            else
                return self::$_Counters['T'][$Key] = round((microtime(true) - self::$_Ticks['T'][$Key])*1000,2);
        }

        public static function MStart ($Key)
        {
            return self::$_Ticks['M'][$Key] = memory_get_peak_usage(true);
        }

        public static function MStop ($Key)
        {
            return self::$_Counters['M'][$Key] += memory_get_peak_usage(true) - self::$_Ticks['M'][$Key];
        }

        public static function SR71()
        {
           $Summary['Time'] = array_sum(self::$_Counters['T']);
           $Summary['Calls'] = array_sum(self::$_Counters['C']);

           arsort(self::$_Counters['T']);
           echo 'Memory: '.round(self::$_Memory/1024)." Kb. <pre>time\tcalls\trtime\tTC\trcall\tfn\n".$Summary['Time']."\t".$Summary['Calls']
               ."\t100%\t100%\n";
               foreach (self::$_Counters['T'] as $Key => $Value)
                   echo implode("\t", [
                            round($Value),
                            self::$_Counters['C'][$Key],
                            round(($Value/$Summary['Time'])*100,2),
                            round($Value/self::$_Counters['C'][$Key], 2),
                            round((self::$_Counters['C'][$Key]/$Summary['Calls'])*100,2),
                            $Key
                        ])."\n";

           echo '</pre>';
        }

        public static function setLive($Live)
        {
            return self::$_Live = (bool) $Live;
        }

        public static function getLive()
        {
            return self::$_Live;
        }

        public static function Reload ()
        {
            foreach (self::$_Options as $Service)
                F::loadOptions($Service);

            foreach (self::$_Code as $Service)
                F::_loadSource($Service);

            return true;
        }

        public static function Shutdown()
        {
            // foreach (self::$_Log as $Line)
            //    echo implode ("\t", $Line).PHP_EOL;

            self::Stop(self::$_Service . '.' . self::$_Method);

            ob_flush();

            $E = error_get_last();

            if (!empty($E) and self::$_Environment != 'Production')
            {
                $Logs = F::Logs();
                foreach ($Logs as $Log)
                    echo implode(' ', $Log).'<br/>';
            }



            if (self::$_SR71)
            {
                self::$_Memory = memory_get_usage();
                self::SR71();
            }

            return false;
        }
    }

    function d()
    {
        if (F::Environment() != 'Production')
            call_user_func_array(['F','Dump'], func_get_args());

        return func_get_arg(2);
    }

    function setFn($Name, $Function)
    {
        return F::setFn($Name, $Function);
    }