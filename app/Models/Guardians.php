<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardians extends Model
{
    use HasFactory;

    protected $fillable = [
        //First stage of Data
       'user_id',

       //Second stage of data
       'profile_photo',
       'gender',
       'date_of_birth',
       'marital_status',
       'phone_number',
       'alt_phn_number',
       'home_address',
       'state_of_origin',
       'local_government_area',

        //Third stage Occupation
       'employment_status',
       'nature_of_occupation',
       'annual_income',
       'employer_name',
       'employer_phone',
       'employer_address',

        // Fourth Stage Identity
       'mean_of_identity',
       'identity_number',
   ];

     // Define the relationship - a Guardian has many Users
     public function user()
     {
         return $this->belongsTo(User::class, 'user_id');
     }
     public function orphans()
     {
         return $this->hasMany(Orphans::class);
     }
}
