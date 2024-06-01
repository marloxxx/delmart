<?php

namespace App\Http\Controllers\API;

use App\Models\SocialAccount;
use Carbon\Carbon;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use App\Models\VerificationCode;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Notifications\RegistrationNotification;
use App\Notifications\ForgotPasswordNotification;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Register a User.
     *
     * @return ResponseFormatter|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|numeric|digits_between:16,16|unique:users',
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'numeric|digits_between:10,13',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'error' => $validator->errors()
            ], 'Validation Error', 401);
        }

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        $user = User::create($data);
        $user->assignRole('customer');
        Auth::user($user);

        try {
            $user->notify(RegistrationNotification::class);
        } catch (Exception $e) {
            return ResponseFormatter::error([
                'message' => 'Failed to send email',
            ], 'Registration Failed', 500);
        }

        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return ResponseFormatter::success([
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
            'user' => $user
        ], 'User Registered');
    }

    /**
     * Login
     *
     * @return ResponseFormatter|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'error' => $validator->errors()
            ], 'Validation Error', 401);
        }

        $user = User::where('email', $request->email)->first();
        $credential = $request->only('email', 'password');
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                if (!Auth::attempt($credential)) {
                    return ResponseFormatter::error([
                        'message' => 'Unauthorized'
                    ], 'Authentication Failed', 500);
                }
            } else {
                return ResponseFormatter::error([
                    'message' => 'Password mismatch'
                ], 'Authentication Failed', 500);
            }
        } else {
            return ResponseFormatter::error([
                'message' => 'User not found'
            ], 'Authentication Failed', 500);
        }

        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return ResponseFormatter::success([
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
            'user' => $user
        ], 'User Logged In');
    }



    /**
     * Forgot Password
     *
     * @return ResponseFormatter|\Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'error' => $validator->errors()
            ], 'Validation Error', 401);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return ResponseFormatter::error([
                'message' => 'User not found'
            ], 'User not found', 404);
        }

        $response = $this->sendOTP($user);

        if ($response['status'] == 'success') {
            return ResponseFormatter::success([
                'message' => 'OTP sent to your email'
            ], 'OTP sent');
        } else {
            return ResponseFormatter::error([
                'message' => 'Something went wrong'
            ], 'OTP failed', 500);
        }
    }

    /**
     * Reset Password
     *
     * @return ResponseFormatter|\Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email|required',
            'code' => 'required',
            'password' => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'error' => $validator->errors()
            ], 'Validation Error', 401);
        }

        $response = $this->verifyOTP($request->email, $request->code);

        if ($response['status'] == 'success') {
            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            return ResponseFormatter::success([
                'message' => 'Password reset successfully'
            ], 'Password reset successfully');
        } else {
            return ResponseFormatter::error([
                'message' => 'Something went wrong'
            ], 'Password reset failed', 500);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return ResponseFormatter|\Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return ResponseFormatter::success([], 'Token Revoked');
    }

    /**
     * Send OTP to user's email
     *
     * @param $user
     *
     * @return array
     */
    public function sendOTP($user): array
    {
        // generate OTP
        $otp = rand(100000, 999999);

        // send OTP to user's email
        try {
            $user->notify(new ForgotPasswordNotification($user->verification_code));
        } catch (Exception $e) {
            return array('status' => 'failed', 'message' => 'Something went wrong');
        }

        // save OTP to user's database
        VerificationCode::create([
            'user_id' => $user->id,
            'code' => $otp,
            'expired_at' => Carbon::now()->addMinutes(1)
        ]);

        return array('status' => 'success', 'message' => 'OTP sent');
    }

    /**
     * Verify OTP
     *
     * @param  string $email
     * @param  string $code
     * @return array
     */
    public function verifyOTP($email, $code)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return array('status' => 'failed', 'message' => 'User not found');
        }

        $verificationCode = VerificationCode::where('code', $code)->first();

        if (!$verificationCode) {
            return array('status' => 'failed', 'message' => 'Invalid OTP');
        }

        $now = Carbon::now();
        if ($verificationCode->expired_at->gt($now)) {
            $verificationCode->delete();
            return array('status' => 'success', 'message' => 'OTP verified');
        }

        $verificationCode->delete();

        return array('status' => 'failed', 'message' => 'OTP expired');
    }

    /**
     * Request Token from Provider
     * @param $provider
     * @param Request $request
     * @return ResponseFormatter|\Illuminate\Http\JsonResponse
     */
    public function requestTokenProvider($provider, Request $request)
    {
        // Getting the user from socialite using token from provider
        try {
            $user = Socialite::driver($provider)->stateless()->userFromToken($request->token);
        } catch (Exception $e) {
            return ResponseFormatter::error([
                'message' => 'Unauthorized'
            ], 'Unauthorized', 401);
        }

        // Check if the user is already registered
        $authUser = $this->findOrCreateUser($user, $request->provider);
        // Login the user
        Auth::login($authUser, true);
        // Create token
        $token = $authUser->createToken('authToken')->plainTextToken;

        // Return the user
        return ResponseFormatter::success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $authUser
        ], 'Authenticated');
    }

    /**
     * Find or create user
     * @param $user
     * @param $provider
     * @return mixed
     */
    public function findOrCreateUser($user, $provider)
    {
        // Check if the user is already registered
        $authUser = SocialAccount::where('provider_id', $user->id)->first();
        if ($authUser) {
            return $authUser->user;
        }

        // Check if the user is already registered
        $authUser = User::where('email', $user->email)->first();
        if ($authUser) {
            return $authUser;
        }

        // Create new user
        $newUser = User::create([
            'name' => $user->name,
            'email' => $user->email,
        ]);

        // Create new social account
        $newUser->socialAccounts()->create([
            'provider_id'   => $authUser->getId(),
            'provider_name' => $provider
        ]);

        return $newUser;
    }
}
