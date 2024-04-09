<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;

// Authorization: Bearer <token>
class AuthController extends Controller
{
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            $response = 'Logged out successfully';
            return $this->successResponse($response);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            if (!$user->hasVerifiedEmail()) {
                return response()->json("You need to verify your email to log in.", 401);
            }

            $response = $user->createToken('api')->plainTextToken;
            return $this->successResponse($response);

        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));

            $response = 'User registered successfully. Please check your email to verify your account.';
            return $this->successResponse($response);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return $this->successResponse($status);
            }

            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->save();

                    $user->tokens()->delete();
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return $this->successResponse($status);
            }

            return response()->json(['email' => [__($status)]], 422);

        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function sendVerificationEmail(Request $request)
    {
        try {
            $request->user()->sendEmailVerificationNotification();
            return $this->successResponse('Verification link sent!');
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function verifyEmail(Request $request)
    {
        try {
             // Manually verify the URL signature...
            if (! URL::hasValidSignature($request)) {
                return response()->json(['message' => 'Invalid verification link or signature.'], 422);
            }

            // Decode the hash...
            $userId = $request->route('id');
            $user = User::find($userId);

            if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
                return response()->json(['message' => 'Invalid verification link or hash.'], 422);
            }

            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Email address is already verified.']);
            }

            $user->markEmailAsVerified();

            return response()->json(['message' => 'Email has been verified successfully!']);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


}
