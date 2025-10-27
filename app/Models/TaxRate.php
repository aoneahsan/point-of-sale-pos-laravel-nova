<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxRate extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'code', 'rate', 'active', 'is_default', 'description'];
    protected $casts = ['rate' => 'float', 'active' => 'boolean', 'is_default' => 'boolean'];
    public function scopeActive($query) { return $query->where('active', true); }
}
