<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Prevent Duplicate Jobs Configuration
    |--------------------------------------------------------------------------
    |
    | Enabling 'Prevent Duplicate Jobs' stops the creation of multiple jobs for
    | the identical entry, crucial when edits are frequent and saves are many.
    | This setting relies on a database and an asynchronous queue system.
    |
    */

    'prevent_duplicate_jobs' => false,

    /*
    |--------------------------------------------------------------------------
    | Database Configuration for Social Media Image Tasks
    |--------------------------------------------------------------------------
    |
    | Some features, such as preventing duplicate jobs, requires a database
    | connection. The following configuration can be used to change the
    | database connection name, as well as the database table used.
    |
    */

    'database' => [
        'connection' => config('database.default', 'mysql'),
        'table' => 'social_media_image_tasks',
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration for Image Jobs
    |--------------------------------------------------------------------------
    |
    | The 'Queue' configuration tailors how social media image jobs are queued,
    | enabling the choice of a specific driver or queue name for dispatching.
    |
    */

    'queue' => [
        'connection' => config('queue.default', 'sync'),
        'name' => 'default',
    ],

];
