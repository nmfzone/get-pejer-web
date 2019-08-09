<?php

return [

    'model' => [
        'created' => ':model has been successfully created.',
        'created_to' => ':model has been successfully added to :parent.',
        'updated' => ':model has been successfully updated.',
        'deleted' => ':model has been successfully deleted.',
        'deleted_from' => ':model has been successfully deleted from :parent.',
        'not_found' => ':model with the given :key not found in our database.',
        'cant_deleted' => 'This :model can\'t be deleted.',
    ],

    'codes' => [
        401 => 'Unauthenticated.',
        403 => 'You don\'t have permission to access this endpoint!',
        404 => 'Endpoint not found or wrong HTTP method.',
        422 => 'The given data was invalid.',
        500 => 'Something went wrong on the server!',
    ],

];
