<?php

namespace App\Http\Controllers;

use App\Http\Requests\FirebaseStaticNotificationRequest;
use App\Jobs\DeleteFirebaseTokens;
use App\Models\GuestNotifiable;
use Illuminate\Http\Request;

class NotifiablesController extends Controller
{
    public function store_or_update(Request $request) {

        $data = $request->validate([
            'token' => ['required', 'string'],
            'locale' => ['required', 'in:ar,en']
        ]);

        $user = null;
        if ($token = $request->bearerToken()) {
            $user = \Laravel\Sanctum\PersonalAccessToken::findToken($token)?->tokenable;
        }

        if($user) {
            $ok = $user->update(['fcm' => $data['locale'] . $data['token']]);
        }

        $ok = GuestNotifiable::updateOrCreate(
        ['token' => $data['token']],
        ['locale' => $data['locale'], 'last_used_at' => now()]);

        DeleteFirebaseTokens::dispatch();

        return $this->generalResponse(null, null, $ok ? 200 : 400);
    }

    public function send(FirebaseStaticNotificationRequest $request) {
        return $request->send();
    }
}
