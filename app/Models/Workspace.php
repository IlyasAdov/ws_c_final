<?php

namespace App\Models;

use App\Models\Service;
use App\Models\ApiToken;
use App\Models\BillingQuota;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Workspace extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function apiTokens() {
        return $this->hasMany(ApiToken::class);
    }

    public function billingQuota() {
        return $this->hasOne(BillingQuota::class);
    }

    public function getBill($workspaceID = null, $year = null, $month = null) {
        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;

        $services = Service::all()->keyBy('id');
        $apiTokens = $this->apiTokens()->with('usages')->get();

        if ($workspaceID) {
            $apiTokens = $this->apiTokens()->where('workspace_id', $workspaceID)->with('usages')->get();
        }

        $total = $apiTokens->reduce(function ($totalCost, $apiToken) use ($services, $year, $month) {
            return $totalCost + $apiToken->usages->reduce(function ($totalToken, $usage) use ($services) {
                return $totalToken + $usage->duration_in_ms * $services[$usage->service_id]->cost_per_ms;
            }, 0);
        }, 0);

        return [
            'apiTokens' => $apiTokens,
            'services' => $services,
            'total' => $total
        ];
    }
}
