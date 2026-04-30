<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\OtpEmailVerification;
use App\Notifications\PasswordRecoveryNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            "first_name" => $data['first_name'],
            "last_name" => $data['last_name'],
            "email" => $data['email'],
            "password" => Hash::make($data['password'])
        ]);

        $this->sendOtp($user);

        event(new Registered($user));

        return response()->json([
            'message' => 'Registrazione completata. Controlla la tua email per il codice OTP.',
            'user' => new UserResource($user),
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json([
                "message" => "Credenziali non valide"
            ], 401);
        }

        $user = User::where('email', $credentials['email'])->first();

        if (!$user->email_verified_at) {
            Auth::logout();
            return response()->json([
                "message" => "Devi verificare la tua email prima di accedere. Controlla la tua casella di posta.",
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            "message" => "Token generato con successo",
            "user" => new UserResource($user),
            "token" => $token
        ]);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Utente non trovato'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email già verificata'], 422);
        }

        if (!$user->otp_code || !$user->otp_expires_at) {
            return response()->json(['message' => 'Codice OTP non trovato. Richiedi un nuovo codice.'], 422);
        }

        if (now()->isAfter($user->otp_expires_at)) {
            return response()->json(['message' => 'Il codice OTP è scaduto. Richiedi un nuovo codice.'], 422);
        }

        if (!Hash::check($request->code, $user->otp_code)) {
            return response()->json(['message' => 'Codice OTP non valido'], 422);
        }

        $user->email_verified_at = now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Email verificata con successo',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    public function resendEmailVerify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Utente non trovato'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email già verificata'], 422);
        }

        $this->sendOtp($user);

        return response()->json(['message' => 'Nuovo codice OTP inviato']);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->json([
            "message" => "Logout effettuato con successo"
        ]);
    }

    public function logoutAll()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            "message" => "Logout da tutti i dispositivi effettuato con successo"
        ]);
    }

    public function user()
    {
        return new UserResource(Auth::user());
    }

    public function sendPasswordRecovery(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->email_verified_at) {
            return response()->json(['message' => 'Dati non validi'], 400);
        }

        $token = Str::random(64);
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        $resetLink = "{$frontendUrl}/password-recovery?token={$token}&email=" . urlencode($request->email);

        $user->update([
            'password_recovery_code' => Hash::make($token),
            'password_recovery_at' => now(),
        ]);

        $user->notify(new PasswordRecoveryNotification($resetLink));

        return response()->json(['message' => 'Email di recupero password inviata']);
    }

    public function passwordRecovery(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8',
            'passwordConfirmation' => 'required|string',
        ]);

        if ($request->password !== $request->passwordConfirmation) {
            return response()->json(['message' => 'Le password non coincidono'], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->password_recovery_code) {
            return response()->json(['message' => 'Link non valido'], 400);
        }

        if (!Hash::check($request->token, $user->password_recovery_code)) {
            return response()->json(['message' => 'Link non valido'], 400);
        }

        $minutesPassed = now()->diffInMinutes($user->password_recovery_at);
        if ($minutesPassed > 10) {
            return response()->json(['message' => 'Link scaduto'], 400);
        }

        $user->password = Hash::make($request->password);
        $user->password_recovery_code = null;
        $user->password_recovery_at = null;
        $user->save();

        return response()->json(['message' => 'Password modificata con successo']);
    }

    private function sendOtp(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        $user->update([
            'otp_code' => Hash::make($code),
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        $user->notify(new OtpEmailVerification($code));
    }
}
