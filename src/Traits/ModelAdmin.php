<?php

namespace Geeklearners\Traits;

trait ModelAdmin
{
    public static $form_submit_button = ['type' => 'button', 'value' => "Submit", 'class' => 'btn btn-primary'];
    public static $form_update_button = ['type' => 'button', 'value' => "Update", 'class' => 'btn btn-primary'];
    // public static $do_not_translate = [];
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
    public function getActionColumn()
    {
        return $this->getEditAction() . " | " . $this->getDeleteAction();
    }
    public function getEditAction()
    {
        return "<a href='" . route(config('admin.prefix') . '.edit', ['model' => app('request')->segment(2), 'id' => $this->id]) . "' class='btn btn-success btn-sm fa fa-edit'>Edit</a>";
    }
    public function getDeleteAction()
    {
        return "<form method='post' style='display:inline' action='" . route(config('admin.prefix') . '.destroy', ['model' => app('request')->segment(2), 'id' => $this->id]) . "'><input type='hidden' name='_method' value='delete'><input type='hidden' name='_token' value='" . csrf_token() . "'><input type='submit' value='Delete' class='btn btn-danger btn-sm'></form>";
    }
    public function __get($key)
    {
        if (property_exists($this, 'do_not_translate')) {
            if (
                is_array(get_called_class()::$do_not_translate)
                && in_array($key, get_called_class()::$do_not_translate)
                && $this->attributes['lang'] !== config('admin.default_language')['code']
                && $this->getDefaultModel()
            ) {
                return $this->getDefaultModel()->attributes[$key];
            }
        }
        return parent::__get($key);
    }
    public $defaultModel = null;
    public function getDefaultModel()
    {
        if (!$this->defaultModel) {
            $this->defaultModel
                = $this->where('uuid', $this->attributes['uuid'])
                ->where('lang', config('admin.default_language')['code'])->first();
        }
        return $this->defaultModel;
    }
}
