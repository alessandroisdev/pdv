<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Active System Modules
    |--------------------------------------------------------------------------
    |
    | Define the list of active modules. The ModuleServiceProvider will loop
    | through these and load their routes, views, and migrations automatically.
    |
    */
    'modules' => [
        'Core',
        'AccessControl',
        'HR',         // Adicionando o Módulo de Recursos Humanos
        'Inventory',
        'Purchasing',
        'Sales',
        'Finance',
        'Settings',
    ],
];
