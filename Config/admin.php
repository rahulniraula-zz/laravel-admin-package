<?php

use App\Models\Contact\Contact;
use App\Models\Contact\ContactType;
use Illuminate\Database\Schema\Blueprint;

return [
    'prefix' => 'admin',

    'crud_classes' => [
        ContactType::class,
        Contact::class
    ],
    'base_admin_layout' => 'admin::default_layout'
];
