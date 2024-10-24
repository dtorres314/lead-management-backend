<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Define the relationship to the Lead model
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}