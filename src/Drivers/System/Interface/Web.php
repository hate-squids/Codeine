<?php

    /* Codeine
     * @author BreathLess
     * @description Web Interface 
     * @package Codeine
     * @version 7.1
     */

    setFn ('Do', function ($Call)
    {
        F::Log('Interface: *Web*', LOG_INFO);

        // HTTP Method determining
        $Call['HTTP']['Method'] =
            in_array($_SERVER['REQUEST_METHOD'], $Call['HTTP']['Methods']['Allowed'])?
            $_SERVER['REQUEST_METHOD']:
            $Call['HTTP']['Methods']['Default'];

        F::Log('Method: *'.$Call['HTTP']['Method'].'*', LOG_INFO);

        // Merge FILES to REQUEST.
        if (isset($_FILES['Data']))
            foreach ($_FILES['Data']['tmp_name'] as $IX => $Value)
                foreach ($Value as $K2 => $V2)
                    if (!empty($V2))
                        $_REQUEST['Data'][$IX][$K2] = $V2;

        // Request reading
        $Call['Request'] = $_REQUEST;
        F::Log($Call['Request'], LOG_INFO);

        // Cookie reading
        $Call['HTTP']['Cookie'] = $_COOKIE;
        F::Log($Call['HTTP']['Cookie'], LOG_INFO);

        // Query string reading

        $Call['HTTP']['URI'] = rawurldecode($_SERVER['REQUEST_URI']).(empty($Call['HTTP']['URL Query'])? '?' : '');
        F::Log('URI: *'.$Call['HTTP']['URI'].'*', LOG_INFO);

        $Call['HTTP']['URL'] = parse_url($Call['HTTP']['URI'], PHP_URL_PATH);
        F::Log('URL: *'.$Call['HTTP']['URI'].'*', LOG_INFO);

        $Call['HTTP']['URL Query'] = parse_url($Call['HTTP']['URI'], PHP_URL_QUERY);
        empty($Call['HTTP']['URL Query'])?
            F::Log('Empty query string.', LOG_INFO):
            F::Log('Query string: *'.$Call['HTTP']['URL Query'].'*', LOG_INFO);

        $Call['Run'] = $Call['HTTP']['URI'];
        F::Log('Run String: '.$Call['Run'], LOG_INFO);

        $Call = F::Apply(null, 'Protocol', $Call);

        $Call['UA'] = F::Live($Call['UA'], $Call);
        $Call['IP'] = F::Live($Call['IP'], $Call);
        $Call['Locale'] = F::Live($Call['Locale'], $Call);

        $Call = F::Hook('beforeInterfaceRun', $Call);

        $Call = F::Apply($Call['Service'], $Call['Method'], $Call);

/*        if (isset($Call['Output']))
            $Call['HTTP']['Headers']['Content-Length:'] = strlen($Call['Output']);*/

        if (isset($Call['HTTP']['Headers']))
            foreach ($Call['HTTP']['Headers'] as $Key => $Value)
                header ($Key . ' ' . $Value);

        F::Run('IO','Write', $Call,
            [
                'Storage' => 'Output',
                'Where' => $Call['HTTP']['URL'],
                'Data' => $Call['Output']
            ]);

        $Call = F::Hook('afterInterfaceRun', $Call);

        return $Call;
    });

    setFn('Redirect', function ($Call)
    {
        $URL = $Call['Location'];

        if (preg_match_all('@\$([\.\w]+)@', $URL, $Vars))
        {
            foreach ($Vars[0] as $IX => $Key)
                $URL = str_replace($Key, F::Dot($Call,$Vars[1][$IX]) , $URL);
        }

        $Call['HTTP']['Headers']['HTTP/1.1'] = ' 301 Moved Permanently';
        $Call['HTTP']['Headers']['Location:'] = $URL;

        F::Log('Redirected to '.$URL, LOG_INFO);

        return $Call;
    });

    setFn('Remote Redirect', function ($Call)
    {
        $URL = $Call['Location'];

        if (preg_match_all('@\$([\.\w]+)@', $URL, $Vars))
        {
            foreach ($Vars[0] as $IX => $Key)
                $URL = str_replace($Key, F::Dot($Call,$Vars[1][$IX]) , $URL);
        }

        $Call['HTTP']['Headers']['HTTP/1.1'] = ' 301 Moved Permanently';
        $Call['HTTP']['Headers']['Location:'] = 'http://'.$URL;

        F::Log('Redirected to '.$URL, LOG_INFO);

        return $Call;
    });

    setFn('StoreURL', function ($Call)
    {
        if(!isset($Call['Request']['BackURL']) && isset($_SERVER['HTTP_REFERER']))
        {
            $Call['BackURL'] = $_SERVER['HTTP_REFERER'];
            F::Log('Back URL set to *'.$Call['BackURL'].'*', LOG_INFO);
        }

        return $Call;
    });

    setFn('RestoreURL', function ($Call)
    {
        if (isset($Call['Request']['BackURL']) && !empty($Call['Request']['BackURL']))
            $Call = F::Apply('System.Interface.Web', 'Redirect', $Call, ['Location' => $Call['Request']['BackURL']]);
        elseif (isset($_SERVER['HTTP_REFERER']))
            $Call = F::Apply('System.Interface.Web', 'Redirect', $Call, ['Location' => $_SERVER['HTTP_REFERER']]);

        return $Call;
    });

    setFn('Protocol', function ($Call)
    {
        if (isset($Call['Project']['Hosts'][F::Environment()]))
        {
            if (preg_match('/(\S+)\.'.$Call['Project']['Hosts'][F::Environment()].'/', $_SERVER['HTTP_HOST'], $Subdomains)
            && isset($Call['Subdomains'][$Subdomains[1]]))
            {
                $Call = F::Merge($Call, $Call['Subdomains'][$Subdomains[1]]);
                F::Log('Active Subdomain detected: '.$Subdomains[1], LOG_INFO);
            }

            $_SERVER['HTTP_HOST'] = $Call['Project']['Hosts'][F::Environment()];
        }

        if ((isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])) or
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
            {
                $Call['HTTP']['Proto'] = 'https://';
                $Call['HTTP']['Host'] = $_SERVER['HTTP_HOST'];
            }
            else
            {
                $Call['HTTP']['Proto'] = 'http://';
                $Call['HTTP']['Host'] = $_SERVER['HTTP_HOST'];
            }

        F::Log('Protocol is *'.$Call['HTTP']['Proto'].'*', LOG_INFO);
        F::Log('Host is *'.$Call['HTTP']['Host'].'*', LOG_INFO);

        $Call = F::loadOptions($Call['HTTP']['Host'], null, $Call);

        return $Call;
    });