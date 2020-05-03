<?php

namespace Geeklearners\Util;

use Illuminate\Support\Str;

class Util
{
    public static function prepareRoutingRegex()
    {
        $classes = config('admin.crud_classes');
        $valid_class = array_map(function ($class) {
            $paths = explode('\\', $class);
            return $paths[count($paths) - 1];
        }, $classes);
        return implode("|", array_map(function ($c) {
            return Str::kebab($c);
        }, $valid_class));
    }
    public static function buildForm($field, $metadata)
    {
        $str = "";
        if ($metadata['type'] == 'text') {
            $str .= \Form::text($field, old($field), $metadata);
        } elseif ($metadata['type'] == 'textarea') {
            $str .= \Form::textarea($field, '', $metadata);
        } elseif ($metadata['type'] == 'select') {
            $str .= \Form::select($field, $metadata['options'], null, array_filter($metadata, function ($m) {
                return $m != "options";
            }, ARRAY_FILTER_USE_KEY));
        } elseif ($metadata['type'] == 'checkbox') {
            $str .= "<br>";
            foreach ($metadata['options'] as $key => $value) {
                $str .= \Form::checkbox($field . '[]', $key, null, ['class' => 'form-check-inline']) . $value . "<br>";
            }
        } elseif ($metadata['type'] == 'radio') {
            $str .= "<br>";
            foreach ($metadata['options'] as $key => $value) {
                $str .= \Form::radio($field, $key, null, ['class' => 'form-check-inline']) . $value . "<br>";
            }
        } else {
            $str .= \Form::text($field, '', $metadata);
        }
        return $str;
    }
}
