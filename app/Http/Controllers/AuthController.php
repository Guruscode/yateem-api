<?php
namespace App\Http\Controllers;

use App\Models\Guardians;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
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
            'account_type' => 'required',
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
public function addProfile(Request $request) {
    $user = auth()->user();

    // Check if the user is a guardian
    if ($user->account_type === 'GUARDIAN') {
        $validator = Validator::make($request->all(), [
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
            'mean_of_identity' => 'required|string|max:100',
            'identity_number' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create or update guardian profile
        $guardian = Guardians::updateOrCreate(
            ['user_id' => $user->id], // Search criteria
            $request->all() // Data to be updated or created
        );

        return response()->json([
            'status' => true,
            'message' => 'Guardian profile updated successfully',
            'guardian' => $guardian
        ], 201);
    } else {
        return response()->json(['error' => 'Only guardians can update their profile'], 403);
    }
}

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


public function addOrphan(Request $request) {
    // Retrieve the authenticated user
    $user = auth()->user();

    // Check if the user is a guardian
    if ($user->account_type !== 'GUARDIAN') {
        return response()->json(['error' => 'Only guardians can add orphans'], 403);
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
        'profile_photo' => 'required|string',
        'gender' => 'required|string|in:FEMALE,MALE',
        'date_of_birth' => 'required|date',
        'state_of_origin' => 'required|string|max:100',
        'local_government' => 'required|string|max:100',
        'in_school' => 'required|boolean',
        'school_name' => 'nullable|string|max:255',
        'school_address' => 'nullable|string|max:255',
        'school_contact_person' => 'nullable|string|max:255',
        'phone_number_of_contact_person' => 'nullable|string|max:100',
        'class' => 'nullable|string|max:100',
        'account_status' => 'nullable|string|in:PENDING,APPROVED,REJECTED',
        'unique_code' => 'nullable|string|max:255',
    ]);

    // Check for validation failure
    if ($validator->fails()) {
        return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 422);
    }

    // Generate a unique code for the orphan
    $uniqueCode = 'NY-' . mt_rand(10000000, 99999999);

    // Merge the unique code with validated data
    $validatedData = $validator->validated();
    $validatedData['unique_code'] = $uniqueCode;

    // Attempt to create the orphan associated with the guardian
    try {
        
        $orphan = $guardian->orphans()->create($validatedData);
        return response()->json([
            'status' => true,
            'message' => 'Orphan added successfully',
            'orphan' => $orphan
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'error' => 'Failed to add orphan',
            'details' => $e->getMessage()
        ], 500);
    }
}



public function editOrphan(Request $request, $id) {
    // Retrieve the authenticated user
    $user = auth()->user();

    // Check if the user is a guardian
    if ($user->account_type !== 'GUARDIAN') {
        return response()->json(['error' => 'Only guardians can edit orphans'], 403);
    }

    // Retrieve the orphan associated with the guardian
    $orphan = $user->guardian->orphans()->find($id);

    // Check if the orphan exists
    if (!$orphan) {
        return response()->json(['error' => 'Orphan not found'], 404);
    }

    // Validate the request data
    $validator = Validator::make($request->all(), [
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'profile_photo' => 'required|string',
        'gender' => 'required|string|in:FEMALE,MALE',
        'date_of_birth' => 'required|date',
        'state_of_origin' => 'required|string|max:100',
        'local_government' => 'required|string|max:100',
        'in_school' => 'required|boolean',
        'school_name' => 'nullable|string|max:255',
        'school_address' => 'nullable|string|max:255',
        'school_contact_person' => 'nullable|string|max:255',
        'phone_number_of_contact_person' => 'nullable|string|max:100',
        'class' => 'nullable|string|max:100',
        'account_status' => 'nullable|string|in:PENDING,APPROVED,REJECTED',
        'unique_code' => 'nullable|string|max:255',
    ]);

    // Check for validation failure
    if ($validator->fails()) {
        return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 422);
    }

    // Update the orphan data
    try {
        $orphan->update($validator->validated());

        return response()->json([
            'status' => true,
            'message' => 'Orphan updated successfully',
            'orphan' => $orphan
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'error' => 'Failed to update orphan',
            'details' => $e->getMessage()
        ], 500);
    }
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
            // Retrieve the guardian profile associated with the user
            $guardianProfile = $user->guardian;
            
            // Return both user and guardian profiles
            return response()->json([
                'user' => $user,
                // 'guardian_profile' => $guardianProfile,
            ]);
        } else {
            // If the user is not a guardian, return only the user profile
            return response()->json([
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