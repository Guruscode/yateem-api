<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Orphans;
use App\Models\Guardians;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\SponsorshipRequest;
use Illuminate\Support\Facades\DB;
use App\Models\SponsorshipRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use  Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GuardianController extends Controller
{


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
            'in_school' => 'string|required|in:YES,NO',
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
            // Create the orphan without checking if it already exists
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
            'in_school' => 'string|required|in:YES,NO',
            'school_name' => 'nullable|string|max:255',
            'school_address' => 'nullable|string|max:255',
            'school_contact_person' => 'nullable|string|max:255',
            'phone_number_of_contact_person' => 'nullable|string|max:100',
            'class' => 'nullable|string|max:100',
            'account_status' => 'nullable|string|in:PENDING,APPROVED,REJECTED',
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


    public function viewOrphans(Request $request) {
        // Retrieve the authenticated user
        $user = auth()->user();
    
        // Check if the user is a guardian
        if ($user->account_type !== 'GUARDIAN') {
            return response()->json(['error' => 'Only guardians can view orphans'], 403);
        }
    
        // Retrieve the guardian associated with the user
        $guardian = $user->guardian;
    
        // Check if the guardian profile exists
        if (!$guardian) {
            return response()->json(['error' => 'Guardian profile not found'], 404);
        }
    
        // Retrieve orphans associated with the guardian
        $orphans = $guardian->orphans()->get();
    
        // Return the list of orphans
        return response()->json([
            'status' => true,
            'orphans' => $orphans
        ]);
    }
    
    public function createSponsorshipRequest(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'guardian_id' => 'required|exists:guardians,id',
                'orphan_id' => 'required|exists:orphans,id',
                'need' => 'required|in:EDUCATION,HEALTH,CLOTHING,FEEDING',
                'description' => 'required|string',
                'amount_needed' => 'required|numeric|min:0',
                'current_amount' => 'nullable|numeric|min:0',
                'request_status' => 'nullable|string|in:PENDING,APPROVED,REJECTED',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    
        try {
            $sponsorshipRequest = SponsorshipRequests::create($validatedData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        return response()->json($sponsorshipRequest, Response::HTTP_CREATED);
    }

    public function requestDelete($id)
    {
        try {
        // Find the orphan by ID
            $orphan = Orphans::findOrFail($id);

         // Check if the authenticated user is the guardian of the orphan
            if ($orphan->guardian_id !== auth()->id()) {
                throw new \Exception('You do not have permission to delete the orphan.');
            }
                
            // Set the delete_requested flag to true
            $orphan->delete_requested = true;
            $orphan->save();
    
            // Return a success response
            return response()->json(['message' => 'Delete request for the orphan has been sent to the admin.'], 200);
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    
}
