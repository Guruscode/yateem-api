<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Guardians;
use Illuminate\Http\Request;
use  Illuminate\Support\Facades\Validator;

class GuardianController extends Controller
{
    public function accountCompletion (Request $request)
    {
        $validator = Validator::make($request->all(), [
            
            'profile_photo' => 'required|string',
            'gender' => 'required|string|between:2,100',
            'date_of_birth' => 'required',
            'marital_status' => 'required|between:2,100',
            'phone_number' =>'required|between:2,100',
            'alt_phn_number' => 'required|between:2,100',
            'home_address' => 'required|string|between:2,100',
            'state_of_origin' => 'required|string|between:2,100',
            'local_government_area' =>'required|string|between:2,100',
            'employment_status' => 'required|string|between:2,100',
            'nature_of_occupation' =>'required|string|between:2,100',
            'annual_income' => 'required|between:2,100',
            'employer_name' => 'required|string|between:2,100',
            'employer_phone' => 'required|between:2,100',
            'employer_address' => 'required|string|between:2,100',
            'mean_of_identity' =>'required|between:2,100',
            'identity_number' =>'required|between:2,100',
        ]);
       // Retrieve the user by ID
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

      // Get the authenticated user's ID
      $userId = auth()->id();

      // Ensure that the authenticated user exists
      if (!$userId) {
          return response()->json(['error' => 'User not authenticated'], 401);
      }
  
      // Create a new guardian record associated with the authenticated user
      $guardianData = $validator->validated();
      $guardianData['user_id'] = $userId; // Set the user_id
  
      $guardian = Guardians::create($guardianData);
  
      return response()->json([
          'message' => 'Guardian Account successfully completed',
          'guardian' => $guardian,
      ], 201);
    }

    public function updateGuardian (Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'profile_photo' => 'required|string',
            'gender' => 'required|string|between:2,100',
            'date_of_birth' => 'required',
            'marital_status' => 'required|between:2,100',
            'phone_number' =>'required|between:2,100',
            'alt_phn_number' => 'required|between:2,100',
            'home_address' => 'required|string|between:2,100',
            'state_of_origin' => 'required|string|between:2,100',
            'local_government_area' =>'required|string|between:2,100',
            'employment_status' => 'required|string|between:2,100',
            'nature_of_occupation' =>'required|string|between:2,100',
            'annual_income' => 'required|between:2,100',
            'employer_name' => 'required|string|between:2,100',
            'employer_phone' => 'required|between:2,100',
            'employer_address' => 'required|string|between:2,100',
            'mean_of_identity' =>'required|between:2,100',
            'identity_number' =>'required|between:2,100',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
    
        $guardian = Guardians::find($id);
    
        if (!$guardian) {
            return response()->json(['error' => 'Guardian not found'], 404);
        }
    
        // Update the guardian information
        $guardian->update($validator->validated());
    
        return response()->json([
            'message' => 'Guardian information updated successfully',
            'guardian' => $guardian,
        ], 200);
    }
}
