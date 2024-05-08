<?php

namespace App\Models;

use App\Models\Orphans;
use App\Models\Guardians;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrphanActivity extends Model
{
    use HasFactory;
    protected $fillable = [
        'activity', 'description', 'upload_document', 'insert_link', 'name_of_health_facility',
        'card_id_number', 'type_of_disease', 'feeding_program', 'feeding_formula', 'size_of_shirt',
        'size_of_trouser', 'arms_length', 'name_of_school_contact_person', 'phone_number', 'date_of_enrollment'
    ]; 

    public function orphan()
    {
        return $this->belongsTo(Orphans::class);
    }

    public function guardian()
    {
        return $this->belongsTo(Guardians::class);
    }

}
