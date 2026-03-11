<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Repository binding
    |--------------------------------------------------------------------------
    | Set this to your own class that implements FormRepositoryContract.
    | Alternatively bind it manually in AppServiceProvider::register().
    |
    | To get a ready-made Eloquent implementation + migration run:
    |   php artisan livewire-form-builder:publish-stubs
    |--------------------------------------------------------------------------
    */
    'repository' => null,   // e.g. \App\Repositories\LivewireFormBuilderRepository::class

    /*
    |--------------------------------------------------------------------------
    | Route Prefix & Middleware
    |--------------------------------------------------------------------------
    */
    'route_prefix'   => 'livewire-form-builder',
    'middleware'     => ['web', 'auth'],
    'builder_routes' => true,   // set false to disable the built-in CRUD routes

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */
    'per_page' => 25,

    /*
    |--------------------------------------------------------------------------
    | File Upload Storage
    |--------------------------------------------------------------------------
    */
    'disk'             => env('LIVEWIRE_FORM_BUILDER_DISK', 'public'),
    'upload_directory' => 'livewire-form-builder/uploads',
    'max_file_size'    => 10240,  // kilobytes

    /*
    |--------------------------------------------------------------------------
    | Allowed file MIME types for upload fields (empty = all)
    |--------------------------------------------------------------------------
    */
    'allowed_mime_types' => [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain',
    ],

    /*
    |--------------------------------------------------------------------------
    | Additional / custom field types
    | Register your own field type classes here:
    |   'my_type' => \App\FormFields\MyTypeField::class
    |--------------------------------------------------------------------------
    */
    'field_types' => [],

];

