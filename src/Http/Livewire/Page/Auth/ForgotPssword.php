<?php

namespace LaraPlatform\Core\Http\Livewire\Page\Auth;

use Illuminate\Support\Facades\Auth;
use LaraPlatform\Core\Facades\Theme;
use LaraPlatform\Core\Livewire\Modal;

class ForgotPssword extends Modal
{
    public function boot()
    {
        parent::boot();
        Theme::setLayoutNone();
    }
    public $username;
    public $password;
    public $isRememberMe;

    protected $rules = [
        'password' => 'required|min:6',
        'username' => 'required|min:1',
    ];
    public function DoWork()
    {
       
    }
    public function mount()
    {
        $this->setTitle('Login to system');
    }
    public function render()
    {
        return $this->viewModal('core::page.auth.forgot_password');
    }
}
