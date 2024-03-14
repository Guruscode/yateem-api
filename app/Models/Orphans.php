<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orphans extends Model
{
    use HasFactory;

    protected $fillable = [
        'guardian_id',
        'profile_photo',
        'gender',
        'first_name',
        'last_name',
        'guidian_affidavit',
        'state_of_origin',
        'local_government',
        'date_of_birth',
        'in_school',
        'school_name',
        'school_address',
        'school_contact_person',
        'phone_number_of_contact_person',
        'class',
        'account_status',
        'unique_code',
    ];

     
    public function guardian()
    {
        return $this->belongsTo(Guardians::class, 'guardian_id');
    }

    // public function details()
    // {
    //     return $this->hasOne(Details::class);
    // }
}
