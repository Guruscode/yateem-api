<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Orphans;
use App\Models\Guardians;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    // public function createOrphan(Request $request)
    // {
    //     // Check if the user is authenticated and is an admin
    //     if (!Auth::check() || Auth::user()->account_type !== 'ADMIN') {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }
    
    //             // Validate the request data
    //             $validator = Validator::make($request->all(), [
    //             'guardians_id' => 'required|exists:guardians,id',
    //             'first_name' => 'required|string',
    //             'last_name' => 'required|string',
    //             'profile_photo' => 'nullable|string',
    //             'gender' => 'required|string|in:FEMALE,MALE',
    //             'date_of_birth' => 'required|date',
    //             'state_of_origin' => 'required|string|max:100',
    //             'local_government' => 'required|string|max:100',
    //             'in_school' => 'required|in:YES,NO',
    //             'school_name' => 'nullable|string|max:255',
    //             'school_address' => 'nullable|string|max:255',
    //             'school_contact_person' => 'nullable|string|max:255',
    //             'phone_number_of_contact_person' => 'nullable|string|max:100',
    //             'class' => 'nullable|string|max:100',
    //             'account_status' => 'nullable|string|in:PENDING,APPROVED,REJECTED',
    //             'unique_code' => 'nullable|string|max:255',
    //         ]);
    //         if ($validator->fails()) {
    //             return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 422);
    //         }
        
    //         // Retrieve a guardian from the database
    //         $guardian = Guardians::inRandomOrder()->first(); // Get a random guardian, you can change the retrieval logic as per your requirement
        
    //         if (!$guardian) {
    //             return response()->json(['error' => 'No guardians found in the database'], 404);
    //         }
        
    //         try {
    //             // Merge the guardian ID with the validated data
    //             $validatedData = array_merge($validator->validated(), ['guardians_id' => $guardian->id]);
        
    //             // Create the orphan
    //             $orphan = Orphans::create($validatedData);
        
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'Orphan added successfully',
    //                 'orphan' => $orphan
    //             ], 201);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'status' => false,
    //                 'error' => 'Failed to add orphan',
    //                 'details' => $e->getMessage()
    //             ], 500);
    //         }
    //     }

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
            'message' => 'User successfully created',
            'user' => $user
        ], 201);
    }

    public function getUsers() {
        $users = User::all();
        return response()->json(['status' => true, 'users' => $users], 200);
    }

    public function updateUser(Request $request, $id) {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users,email,'.$user->id,
            'account_type' => 'required|string|in:ADMIN,GUARDIAN,SPONSOR',
            // Add other fields as needed
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 422);
        }
        
        $user->update($validator->validated());
        
        return response()->json(['status' => true, 'message' => 'User updated successfully']);
    }
    public function viewUser($id) {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        return response()->json(['status' => true, 'user' => $user], 200);
    }
    
    public function deleteUser($id) {
        // Find the user by ID
        $user = User::find($id);
    
     
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Delete the user
        $user->delete();
    
        return response()->json(['status' => true, 'message' => 'User deleted successfully'], 200);
    }


        // ====================
        //  Controller  for Guardian    ==
        // ====================


    public function createGuardian(Request $request) {
        // Validate the request data
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
    
                // Check for validation failure
                if ($validator->fails()) {
                    return response()->json(['error' => $validator->errors()], 422);
                }

                // Find the authenticated user with account type GUARDIAN
                $user = User::where('account_type', 'GUARDIAN')->first();

                // If no user found, return error response
                if (!$user) {
                    return response()->json(['error' => 'No user with account type GUARDIAN found'], 404);
                }

                // Create the guardian and associate it with the user
                $guardian = $user->guardian()->create($validator->validated());

                return response()->json(['status' => true, 'message' => 'Guardian created successfully', 'guardian' => $guardian], 201);
            }

    public function listGuardians() {
        // Retrieve all guardians
        $guardians = Guardians::all();
    
        return response()->json(['status' => true, 'guardians' => $guardians], 200);
    }
    
    public function viewGuardian($id) {
        // Find the guardian by ID
        $guardian = Guardians::find($id);
    
        // If guardian not found, return error response
        if (!$guardian) {
            return response()->json(['error' => 'Guardian not found'], 404);
        }
    
        return response()->json(['status' => true, 'guardian' => $guardian], 200);
    }
    public function editGuardian(Request $request, $id) {
        // Find the guardian by ID
        $guardian = Guardians::find($id);
    
        // If guardian not found, return error response
        if (!$guardian) {
            return response()->json(['error' => 'Guardian not found'], 404);
        }
    
        // Validate the request data
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
    
        // Check for validation failure
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        // Update the guardian
        $guardian->update($validator->validated());
    
        return response()->json(['status' => true, 'message' => 'Guardian updated successfully', 'guardian' => $guardian], 200);
    }
    
    public function deleteGuardian($id) {
        $guardian = Guardians::find($id);
    
        // If guardian not found, return error response
        if (!$guardian) {
            return response()->json(['error' => 'Guardian not found'], 404);
        }
    
        // Delete the guardian
        $guardian->delete();
    
        return response()->json(['status' => true, 'message' => 'Guardian deleted successfully'], 200);
    }
    
}
