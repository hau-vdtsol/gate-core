<?php

namespace LaraPlatform\Core\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Livewire\Livewire;

class LaraServiceController extends BaseController
{
    public function loadComponent($slug)
    {
        $param = isset(Request::all()['param']) ? preg_replace('/[\x00-\x1F\x80-\xFF]/', '', stripslashes(Request::all()['param'])) : '{}';
        if (is_array($param)) {
            $param = $param[0];
        }
        $param = str_replace("'", "\"", $param);
        $param = json_decode($param, true) ?? [];
        return [
            'html' => Livewire::mount($slug, $param)->html(),
            'slug' => $slug,
            'param' => $param,
        ];
    }
}