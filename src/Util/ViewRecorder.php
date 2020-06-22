<?php

namespace Geeklearners\Util;

use Illuminate\Support\Facades\View;

class ViewRecorder
{
    public $store;

    public function __construct()
    {
        $this->store = [];
        $this->views = [];
    }
    public function register($key, $view)
    {
        if (!array_key_exists($key, $this->store)) {
            $this->store[$key] = $view;
        }
    }
    public function registerViews($key, $view)
    {
        if (!array_key_exists($key, $this->store)) {
            $this->views[$key] = $view;
        }
    }

    public function getContent($key)
    {
        if (array_key_exists($key, $this->views)) {
            return View::exists($this->views[$key]) ? View::make($this->views[$key], $this->store) : '';
        }
    }
}
