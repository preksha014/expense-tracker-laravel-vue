<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::all();
        return response()->json($groups, 200);
        // return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255'
            ]);
    
            $group = Group::create($validated);
    
            return response()->json([
                'message' => 'Group created successfully',
                'group' => $group
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went backend wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function edit(Group $group)
    {
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $group->update($validated);
        return response()->json([
            'message' => 'Group updated successfully',
            'group' => $group
        ], 200);
    }

    public function destroy(Group $group)
    {
        $group->delete();

        return response()->json([
            'message' => 'Group deleted successfully'
        ], 200);
        // return redirect()->route('groups.index')
        //     ->with('success', 'Group deleted successfully.');
    }
}