<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with('leader')->get();
        return view('teams.index', compact('teams'));
    }

    public function create()
    {
        $members = Member::all();
        return view('teams.create', compact('members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'leader_id' => 'nullable|exists:members,id',
        ]);

        Team::create($request->only('name', 'description', 'leader_id'));

        return redirect()->route('teams.index')->with('success', 'Team created');
    }

    public function edit($id)
    {
        $team = Team::findOrFail($id);
        $members = Member::all();
        return view('teams.edit', compact('team', 'members'));
    }

    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);
        $team->update($request->only('name', 'description', 'leader_id'));

        return redirect()->route('teams.index')->with('success', 'Team updated');
    }

    public function destroy($id)
    {
        Team::destroy($id);
        return redirect()->route('teams.index')->with('success', 'Team deleted');
    }

    public function addMember(Request $request, $teamId)
    {
        $request->validate([
            'user_id' => 'required|exists:members,id',
            'position' => 'nullable|string',
        ]);

        TeamMember::create([
            'team_id' => $teamId,
            'user_id' => $request->user_id,
            'position' => $request->position,
            'join_at' => now(),
        ]);

        return redirect()
            ->route('teammembers.index', $teamId)
            ->with('success', 'Member added to team');
    }

    // in TeamController

    /**
     * Handle the PUT /admin/teams/{team}/members/{member}
     */
    public function editMember(Request $request, $teamId, $memberId)
    {
        $request->validate([
            'position' => 'nullable|string',
            'join_at'  => 'nullable|date',
            'leave_at' => 'nullable|date',
        ]);

        $member = TeamMember::findOrFail($memberId);
        $member->update($request->only('position','join_at','leave_at'));

        // redirect to the members LIST, not the “show” URL
        return redirect()
            ->route('teammembers.index', $teamId)
            ->with('success','Team member updated');
    }


    public function deleteMember($teamId, $memberId)
    {
        TeamMember::where("id", $memberId)->delete();

        return redirect()
            ->route('teammembers.index', $teamId)
            ->with('success','Team member removed');
    }


    // GET  /teams/{team}/members
    public function indexMember($teamId)
    {
        $team        = Team::findOrFail($teamId);
        $teamMembers = TeamMember::with('member')
            ->where('team_id', $teamId)
            ->get();

        return view('teammembers.index', compact('team','teamMembers'));
    }

// GET  /teams/{team}/members/create
    public function createMember($teamId)
    {
        $team = Team::findOrFail($teamId);
        return view('teammembers.create', compact('team'));
    }

// GET  /teams/{team}/members/{member}/edit
    public function editMemberForm($teamId, $memberId)
    {
        $team   = Team::findOrFail($teamId);
        $member = TeamMember::with('member')->findOrFail($memberId);

        return view('teammembers.edit', compact('team','member'));
    }
}
