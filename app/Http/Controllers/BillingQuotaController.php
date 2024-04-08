<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;

class BillingQuotaController extends Controller
{
    public function edit($workspaceId) {
        $workspace = Workspace::findOrFail($workspaceId);
        return view('quota.set', ['limit' => optional($workspace->billingQuota)->limit]);
    }

    public function update(Request $req, $workspaceId) {
        $req->validate(['limit' => 'nullable|numeric']);

        $workspace = Workspace::findOrFail($workspaceId);

        $limit = $req->input('limit');

        if ($limit !== null) {
            $workspace->billingQuota()->updateOrCreate(['workspace_id' => $workspaceId, 'limit' => floatval($limit)]);
        } else if ($workspace->billingQuota) {
            $workspace->billingQuota->delete();
        }

        return redirect()->route('workspaces.show', ['workspaceId' => $workspaceId])->with(['action' => 'quotaUpdated']);
    }
}
