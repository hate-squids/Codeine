<?php

    /* Codeine
     * @author BreathLess
     * @description Unified Control 
     * @package Codeine
     * @version 7.x
     */

    setFn('Do', function ($Call)
    {
        return F::Run('Entity.List', 'Do', $Call, ['Entity' => $Call['Bundle'], 'Scope' => 'Control', 'Show Redirects' => true]);
    });

    setFn('Show', function ($Call)
    {
        return F::Run('Entity.Show.Static', 'Do', $Call, ['Entity' => $Call['Bundle'], 'Scope' => 'Control']);
     });

    setFn('Create', function ($Call)
    {
        return F::Run('Entity.Create', 'Do', $Call, ['Entity' => $Call['Bundle'], 'Scope' => 'Control']);
    });

    setFn('Update', function ($Call)
    {
        return F::Run('Entity.Update', 'Do', $Call, ['Entity' => $Call['Bundle'], 'Scope' => 'Control']);
    });

    setFn('Delete', function ($Call)
    {
        $Call['Layouts'][] = ['Scope' => 'Entity', 'ID' => 'Delete'];
        return F::Run('Entity.Delete', 'Do', $Call, ['Entity' => $Call['Bundle'], 'Scope' => 'Control']);
    });

    setFn('Menu', function ($Call)
    {
        $Count = F::Run('Entity', 'Count', $Call, ['Entity' => $Call['Bundle'], 'Scope' => 'Control']);
        return ['Count' => $Count];
    });

    setFn('Export', function ($Call)
    {
        $Elements = F::Run('Entity', 'Read',
                    [
                         'Entity' => $Call['Bundle']
                    ]);

        $Call['Renderer'] = 'View.JSON';
        $Call['Output']['Content'] = $Elements;

        return $Call;
    });

    setFn('Check', function ($Call)
    {
        return F::Run('Entity.Check', 'Do', $Call, ['Entity' => $Call['Bundle']]);
    });

    setFn('Accept', function ($Call)
    {
        $Call['Layouts'][] = ['Scope' => 'Entity', 'ID' => 'Accept'];
        return F::Run('Entity.Accept', 'Do', $Call, ['Entity' => $Call['Bundle']]);
    });

    setFn('Reject', function ($Call)
    {
        $Call['Layouts'][] = ['Scope' => 'Entity', 'ID' => 'Reject'];
        return F::Run('Entity.Reject', 'Do', $Call, ['Entity' => $Call['Bundle']]);
    });

    setFn('Truncate', function ($Call)
    {
        return F::Run('Entity.Truncate', 'Do', $Call, ['Entity' => $Call['Bundle']]);
    });