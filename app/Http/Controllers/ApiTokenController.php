<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use App\Models\Workspace;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    public function create() {
        return view('apiTokens.create');
    }

    public function store(Request $req, $workspaceId) {
        $req->validate([
            'name' => 'required|string|max:100'
        ]);

        $workspace = Workspace::findOrFail($workspaceId);

        $apiToken = $workspace->apiTokens()->create([
            'name' => $req->name,
            'token' => Str::random(50)
        ]);

        return view('apiTokens.show', compact('apiToken', 'workspace'));
    }

    public function destroy($workspaceId, $tokenId) {
        $workspace = Workspace::findOrFail($workspaceId);
        $apiToken = ApiToken::findOrFail($tokenId);

        if ($apiToken->workspace_id === $workspace->id) {
            $apiToken->update(['revoked_at' => now()]);
        }

        return redirect()->route('workspaces.show', ['workspaceId' => $workspace->id])->with(['action' => 'tokenRevoked']);
    }
}
