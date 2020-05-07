<?php

namespace Geeklearners\Traits;

trait ModelAdmin
{
    public static $form_submit_button = ['type' => 'button', 'value' => "Submit", 'class' => 'btn btn-primary'];
    public static $form_update_button = ['type' => 'button', 'value' => "Update", 'class' => 'btn btn-primary'];

    public function getValue($col, $method)
    {
        if (method_exists($this, $method)) {
            $d = $this->$method();
            if (isset($d[$col]) && is_callable($d[$col])) {
                return ($d[$col])($this);
            }
        }
        return $this->$col ?? '';
    }
    public static function getLabel($label)
    {
        if (property_exists(get_called_class(), 'labelTransformer') && isset(get_called_class()::$labelTransformer[$label])) {
            return get_called_class()::$labelTransformer[$label];
        }
        return ucwords($label);
    }
}
