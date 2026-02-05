<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\UserDeviceToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class FacebookAuthController extends Controller
{
    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleFacebookCallback(Request $request)
    {
        // Check if the request has the 'code' parameter from Facebook
        if (!$request->has('code')) {
            return response()->json([
                'success' => false,
                'message' => 'Direct access to this URL is not allowed. Please start login from /api/facebook/redirect',
            ], 400);
        }

        try {
            $fbUser = Socialite::driver('facebook')->stateless()->user();

            if (!$fbUser || !$fbUser->getEmail()) {
                throw new Exception("Could not retrieve email from Facebook profile.");
            }

            $user = User::where('fb_id', $fbUser->id)
                ->orWhere('email', $fbUser->getEmail())
                ->first();

            if ($user) {
                // Update FB ID if not set
                if (!$user->fb_id) {
                    $user->update(['fb_id' => $fbUser->id]);
                }
            } else {
                // Create new user
                $user = User::create([
                    'name' => $fbUser->getName(),
                    'email' => $fbUser->getEmail(),
                    'fb_id' => $fbUser->getId(),
                    'password' => Hash::make(Str::random(24)),
                    'user_role_id' => 2, // Customer
                    'is_active' => 1,
                    'is_deleted' => 0,
                ]);
            }

            $token = $user->createToken('FacebookLoginToken')->accessToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => $user,
                'token' => $token
            ], 200);

        } catch (Exception $e) {
            Log::error('Facebook Auth Error: ' . $e->getMessage());

            // Check if error is related to redirect URI mismatch
            $errorMessage = 'Authentication failed';
            if (str_contains($e->getMessage(), 'redirect_uri_mismatch')) {
                $errorMessage = 'Redirect URI mismatch. Check your Facebook App settings.';
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Facebook Login via Access Token (for Mobile/Web SDKs)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginWithToken(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
        ]);

        try {
            $fbUser = Socialite::driver('facebook')->userFromToken($request->access_token);

            $user = User::where('fb_id', $fbUser->id)
                ->orWhere('email', $fbUser->email)
                ->first();

            if ($user) {
                if (!$user->fb_id) {
                    $user->update(['fb_id' => $fbUser->id]);
                }
            } else {
                $user = User::create([
                    'name' => $fbUser->name,
                    'email' => $fbUser->email,
                    'fb_id' => $fbUser->id,
                    'password' => Hash::make(Str::random(24)),
                    'user_role_id' => 2,
                    'is_active' => 1,
                    'is_deleted' => 0,
                ]);
            }

            // Handle device token if provided
            if ($request->device_type && $request->device_id && $request->device_token) {
                UserDeviceToken::updateOrCreate(
                    ['user_id' => $user->id, 'device_id' => $request->device_id],
                    ['device_type' => $request->device_type, 'device_token' => $request->device_token]
                );
            }

            $token = $user->createToken('FacebookLoginToken')->accessToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => $user,
                'token' => $token
            ], 200);

        } catch (Exception $e) {
            Log::error('Facebook Token Auth Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Token authentication failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
