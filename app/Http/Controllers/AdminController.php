<?php

namespace App\Http\Controllers;

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
    public function createOrphan(Request $request)
    {
        // Check if the user is authenticated and is an admin
        if (!Auth::check() || Auth::user()->account_type !== 'ADMIN') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
                // Validate the request data
                $validator = Validator::make($request->all(), [
                'guardians_id' => 'required|exists:guardians,id',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'profile_photo' => 'nullable|string',
                'gender' => 'required|string|in:FEMALE,MALE',
                'date_of_birth' => 'required|date',
                'state_of_origin' => 'required|string|max:100',
                'local_government' => 'required|string|max:100',
                'in_school' => 'required|in:YES,NO',
                'school_name' => 'nullable|string|max:255',
                'school_address' => 'nullable|string|max:255',
                'school_contact_person' => 'nullable|string|max:255',
                'phone_number_of_contact_person' => 'nullable|string|max:100',
                'class' => 'nullable|string|max:100',
                'account_status' => 'nullable|string|in:PENDING,APPROVED,REJECTED',
                'unique_code' => 'nullable|string|max:255',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 422);
            }
        
            // Retrieve a guardian from the database
            $guardian = Guardians::inRandomOrder()->first(); // Get a random guardian, you can change the retrieval logic as per your requirement
        
            if (!$guardian) {
                return response()->json(['error' => 'No guardians found in the database'], 404);
            }
        
            try {
                // Merge the guardian ID with the validated data
                $validatedData = array_merge($validator->validated(), ['guardians_id' => $guardian->id]);
        
                // Create the orphan
                $orphan = Orphans::create($validatedData);
        
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
}
