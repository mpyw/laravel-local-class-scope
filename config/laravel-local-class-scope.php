<?php

return [
    /*
    |-------------------------------------------------------------
    | Laravel Local Class Scope - Laravel IDE Helper integration
    |-------------------------------------------------------------
    |
    | Model::query()->scoped() can be complemented by duplicated Eloquent Builder class definition.
    | However, Model::scoped() cannot because it is untraceable from extended class.
    | So we need to rewrite \Eloquent section on _ide_helper.php.
    */

    'model_method_completion' => [

        // Enabled by default
        'enabled' => true,

        // Enabled only in local environment by default
        'environments' => ['local'],
    ],
];
