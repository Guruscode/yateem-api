<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    public function forgotPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    // Generate a random six-digit token
    $token = mt_rand(100000, 999999);

    // Save the token and expiry timestamp in the password_resets table
    DB::table('password_resets')->insert([
        'email' => $request->email,
        'token' => $token,
        'created_at' => now(),
        'expires_at' => now()->addHours(1), // Token expires in 1 hour
    ]);

    // Send the password reset email with the token
    Mail::send('emails.reset_password', ['token' => $token], function ($message) use ($request) {
        $message->to($request->email);
        $message->subject('Reset Password');
    });

    // Include the created_at and expires_at timestamps in the JSON response
    return response()->json([
        'message' => 'Reset password Token has been sent to your email address.',
        'created_at' => now(),
        'expires_at' => now()->addHours(1),
    ]);
}

public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'token' => 'required|digits:6', // Ensure the token is exactly six digits
        'password' => 'required|string|confirmed',
    ]);

    $tokenData = DB::table('password_resets')
        ->where('email', $request->email)
        ->where('token', $request->token)
        ->where('expires_at', '>', now()) // Check if the token is not expired
        ->first();

    if (!$tokenData) {
        // Invalid token or expired
        return response()->json(['error' => 'Invalid or expired token'], 400);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        // User not found
        return response()->json(['error' => 'User not found'], 404);
    }

    // Update the user's password
    $user->password = bcrypt($request->password);
    $user->save();

    // Delete the token from the password_resets table
    DB::table('password_resets')->where('email', $request->email)->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Password has been reset.',
    ]);
}
}
