## Introduction
This package sets up admin pannel in an easy and configurable manner.
## Installation

```
composer require geeklearners/laravel-admin
```
## publish configuration
```
php artisan vendor:publish --tag=geeklearners_admin
```
This publishes `admin.php` file within the config folder.
### Customization details in `admin.php`
`prefix` to be used for routing. The default is set to `admin`.
`crud_classes` is an array containing the models whose crud needs to be generated.
`base_admin_layout` used to specify the base layout used for rendering the admin pannel to.

* All Crud operations are placed within `@section('admin_content')`. So the main admin layout needs to have `@yield('admin_content')` for rendering the content

## Usage Example

```php
use App\Http\Requests\StoreContactTypeRequest;
use Geeklearners\Traits\ModelAdmin;
use Illuminate\Database\Eloquent\Model;

class ContactType extends Model
{
    use ModelAdmin;
    protected $guarded = ['id'];
    /**
     * Form fields to be displayed during creation
     */
    public static function formFields = [
        'name' => [
            'type' => 'text', 'class' => 'form-control'
        ],
        'description' => [
            'type' => 'text', 'class' => 'form-control'
        ],
        'status' => [
            'type' => 'select', 'class' => 'form-control', 'options' => ['1' => 'Active', '0' => 'Inactive']
        ]
    ];

    /**
     * Laravel FormRequest can be injected with the following key which of them will instantiated
     * at appropriate methods
     * 'store'=>...................
     * 'index'=>...................
     * 'update'=>..................
     * 'delete'=>..................
     */
    public static $form_requests = [
        'store' => StoreContactTypeRequest::class
    ];

    /**
     * List out the columns to include when listing the resources.
     * Its an associative array each of the keys receiving optional callback.
     * callback provides the complete row, which can be further customized.
     */
    public static function colsToInclude()
    {
        return [
            'name' => function ($col) {
                return $col->name;
            },
            'description',
            'status'
        ];
    }
    /**
     * List out the additional columns to include when listing the resources.
     * Its an associative array each of the keys receiving optional callback.
     * callback provides the complete row, which can be further customized.
     */
    public static function additionalColumns()
    {
        return ['Actions' => function ($col) {
            return "<a href='" . route('admin.edit', ['model' => 'contact-type', 'id' => $col->id]) . "' class='btn btn-success fa fa-edit'>Edit</a> | <form method='post' style='display:inline' action='" . route('admin.destroy', ['model' => 'contact-type', 'id' => $col->id]) . "'><input type='hidden' name='_method' value='delete'><input type='hidden' name='_token' value='" . csrf_token() . "'><input type='submit' value='Delete' class='btn btn-danger btn-sm'></form>";
        }];
    }
}
```
