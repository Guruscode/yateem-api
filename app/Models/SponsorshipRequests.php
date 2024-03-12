<?php

namespace App\Models;

use App\Models\Orphans;
use App\Models\Guardians;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SponsorshipRequests extends Model
{
    use HasFactory;

    protected $fillable = [
        'guardian_id', 'orphan_id', 'need', 'description', 'amount_needed', 'current_amount'
    ];
 /**
     * Get the guardian that owns the sponsorship request.
     */
    public function guardian()
    {
        return $this->belongsTo(Guardians::class);
    }

    /**
     * Get the orphan associated with the sponsorship request.
     */
    public function orphan()
    {
        return $this->belongsTo(Orphans::class);
    }

}
