<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\MetaConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.registro');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());
        auth()->login($user);

        $eventId = Str::uuid();

        MetaConversionService::completeRegistration($user, $eventId);

        return redirect('/dash')->with('success', "Conta registrada com sucesso.");
    }
}
