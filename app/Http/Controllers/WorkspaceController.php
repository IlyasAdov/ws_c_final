<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class WorkspaceController extends Controller
{
    public function index() {
        $user = Auth::user();
        $workspaces = Workspace::where('user_id', $user->id)->get();

        return view('workspaces.list', compact('workspaces'));
    }

    public function create() {
        return view('workspaces.create');
    }

    public function store(Request $req) {
        $req->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string'
        ]);

        $user = Auth::user();
        $exist = Workspace::where('title', $req->title)->where('user_id', $user->id)->first();
        if ($exist) {
            return redirect()->back()->withErrors(['title' => 'Рабочая область с таким названием уже существует'])->withInput();
        }

        $workspace = Workspace::create([
            'title' => $req->title,
            'description' => $req->description,
            'user_id' => $user->id
        ]);

        return redirect()->route('workspaces.show', ['workspaceId' => $workspace->id]);
    }

    public function show($workspaceId) {
        $workspace = Workspace::findOrFail($workspaceId);
        $costsCurrentMonth = $workspace->getBill($workspaceId)['total'];
        $daysLeftCurrentMonth = Carbon::now()->endOfMonth()->diffInDays();

        $firstMonth = Carbon::createFromFormat('Y-m-d H:i:s', $workspace->created_at)->startOfMonth();
        $numBills = Carbon::now()->diffInMonths($firstMonth) + 1;

        $bills = [];
        for ($i=0; $i < $numBills; $i++) { 
            $bills[] = Carbon::now()->subMonths($i)->startOfMonth();
        }

        return view('workspaces.show', compact('workspace', 'costsCurrentMonth', 'daysLeftCurrentMonth', 'bills'));
    }

    public function edit($workspaceId) {
        $workspace = Workspace::findOrFail($workspaceId);

        return view('workspaces.edit', compact('workspace'));
    }

    public function update(Request $req, $workspaceId) {
        $workspace = Workspace::findOrFail($workspaceId);

        $req->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string'
        ]);

        $user = Auth::user();
        $exist = Workspace::where('title', $req->title)->where('user_id', $user->id)->where('id', '!=', $workspaceId)->first();
        if ($exist) {
            return redirect()->back()->withErrors(['title' => 'Рабочая область с таким названием уже существует'])->withInput();
        }

        $workspace->title = $req->title;
        $workspace->description = $req->description;
        $workspace->save();

        return redirect()->route('workspaces.show', ['workspaceId' => $workspace->id])->with(['action' => 'workspaceUpdated']);
    }
}
