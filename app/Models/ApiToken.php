<?php

namespace App\Models;

use App\Models\ServiceUsage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApiToken extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function usages() {
        return $this->hasMany(ServiceUsage::class);
    }
}
