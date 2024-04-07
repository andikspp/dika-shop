<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Buyer;
use Illuminate\Support\Facades\Password;

class BuyerController extends Controller
{
    public function tampilkanHome()
    {
        return view('back.pages.buyer.home');
    }

    public function login()
    {
        return view('back.pages.buyer.login-buyer');
    }

    public function registerPage()
    {
        return view('back.pages.buyer.register-buyer');
    }

    public function success()
    {
        return view('back.pages.buyer.verification-verify');
    }

    public function forgotPasswordPage()
    {
        return view('back.pages.buyer.forgot-password');
    }

    public function detailProduct()
    {
        return view('back.pages.buyer.detail-product');
    }
}
