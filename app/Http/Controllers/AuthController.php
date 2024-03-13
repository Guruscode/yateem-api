<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Orphans;
use App\Models\Guardians;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password; 
use  Illuminate\Support\Facades\Validator;


class AuthController extends Controller

{
    
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            // 'account_type' => 'required',
            'password' => 'required|string|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json([
                'status' => false,
                'error' => 'Unauthorized',
            ], 401);
        }
    
        return $this->createNewToken($token);
    }
    
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
           
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'account_type' => 'required|string|in:ADMIN,GUARDIAN,SPONSOR',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->toJson(),
            ], 422);
        }
        
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));
        return response()->json([
            'status' => true,
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }
// addAdditionalInfo Method// Update Profile Method

public function editProfile(Request $request, $id) {
    // Retrieve the authenticated user
    $user = auth()->user();

    // Check if the user is authorized to edit the profile
    if ($user->id != $id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Retrieve the guardian associated with the user
    $guardian = $user->guardian;

    // Check if the guardian profile exists
    if (!$guardian) {
        return response()->json(['error' => 'Guardian profile not found'], 404);
    }

    // Validate the request data
    $validator = Validator::make($request->all(), [
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'email' => 'required|string|email|max:100|unique:users,email,'.$user->id,
        'profile_photo' => 'required|string',
        'gender' => 'required|string|in:FEMALE,MALE',
        'date_of_birth' => 'required|date',
        'marital_status' => 'required|string|in:SINGLE,MARRIED,COMPLICATED',
        'phone_number' => 'required|string|max:100',
        'alt_phn_number' => 'nullable|string|max:100',
        'home_address' => 'required|string|max:255',
        'state_of_origin' => 'required|string|max:100',
        'local_government_area' => 'required|string|max:100',
        'employment_status' => 'required|string|in:EMPLOYED,UNEMPLOYED,SELF_EMPLOYED',
        'nature_of_occupation' => 'required|string|max:255',
        'annual_income' => 'required|string|max:100',
        'employer_name' => 'nullable|string|max:255',
        'employer_phone' => 'nullable|string|max:100',
        'employer_address' => 'nullable|string|max:255',
        'mean_of_identity' => 'required|string|in:NATIONAL_ID,VOTERS_CARD,DRIVER_LICENCE,INTERNATIONAL_PASSWORD,PASSPORT',
        'identity_number' => 'required|string',
    ]);

    // Check for validation failure
    if ($validator->fails()) {
        return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 422);
    }

    // Update user data
    $user->update([
        'first_name' => $request->input('first_name'),
        'last_name' => $request->input('last_name'),
        'email' => $request->input('email'),
        'profile_photo' => $request->input('profile_photo'),
        // Add other user fields as needed
    ]);

    // Update guardian data
    $guardian->update([
        ['user_id' => $user->id],
        'profile_photo' => $request->input('profile_photo'),
        'gender' => $request->input('gender'),
        'date_of_birth' => $request->input('date_of_birth'),
        'marital_status' => $request->input('marital_status'),
        'phone_number' => $request->input('phone_number'),
        'alt_phn_number' => $request->input('alt_phn_number'),
        'home_address' => $request->input('home_address'),
        'state_of_origin' => $request->input('state_of_origin'),
        'local_government_area' => $request->input('local_government_area'),
        'employment_status' => $request->input('employment_status'),
        'nature_of_occupation' => $request->input('nature_of_occupation'),
        'annual_income' => $request->input('annual_income'),
        'employer_name' => $request->input('employer_name'),
        'employer_phone' => $request->input('employer_phone'),
        'employer_address' => $request->input('employer_address'),
        'mean_of_identity' => $request->input('mean_of_identity'),
        'identity_number' => $request->input('identity_number'),
        // Add other guardian fields as needed
    ]);

    return response()->json(['status' => true, 'message' => 'Profile updated successfully']);
}



public function changePassword(Request $request)
{
    $request->validate([
        'old_password' => 'required|string',
        'password' => 'required|string|confirmed|min:8',
    ]);

    $user = Auth::user();

    // Verify the old password
    if (!Hash::check($request->old_password, $user->password)) {
        return response()->json(['error' => 'Incorrect old password'], 401);
    }

    // Update the user's password
    $user->password = bcrypt($request->password);
    $user->save();

    return response()->json(['message' => 'Password has been changed successfully']);
}
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        // Retrieve the authenticated user
        $user = auth()->user();
    
        // Check if the user is a guardian
        if ($user->account_type === 'GUARDIAN') {
            // Check if the guardian profile is associated with the user
            if ($user->guardian) {
                // Retrieve the guardian profile associated with the user
                $guardianProfile = $user->guardian;
    
                // Retrieve orphans associated with this guardian
                $orphans = $guardianProfile->orphans()->get();
    
                // Return user profile, guardian profile, and orphans
                return response()->json([
                    'user' => $user,
                    'guardian_profile' => $guardianProfile,
                    'orphans' => $orphans,
                ]);
            } else {
                // Return an error response if guardian profile is not found
                return response()->json([
                    'status' => true,
                    'error' => 'Guardian profile not found for this user.',
                ], 404);
            }
        } else {
            // If the user is not a guardian, return only the user profile
            return response()->json([
                'true' => true,
                'user' => $user,
            ]);
        }
    }
    
    
    
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'status' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 120,
            'user' => auth()->user()
        ]);
    }
}