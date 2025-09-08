<?php

namespace App\Http\Controllers;

use App\Jobs\DeleteFirebaseTokens;
use App\Models\GuestNotifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotifiablesController extends Controller
{
    public function store_or_update(Request $request) {

        $data = $request->validate([
            'token' => ['required', 'string'],
            'locale' => ['required', 'in:ar,en']
        ]);

        $ok = GuestNotifiable::updateOrCreate(['token' => $data['token']], ['locale' => $data['locale']]);

        DeleteFirebaseTokens::dispatch();

        return $this->generalResponse(null, null, $ok ? 200 : 400);
    }
}
