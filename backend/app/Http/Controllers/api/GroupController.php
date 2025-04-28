<?php

namespace App\Http\Controllers\api;

use App\Helpers\ApiResponse;
use App\Models\Group;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Resources\GroupResource;

class GroupController extends Controller
{
    public function index()
    {
        try {
            $groups = Group::where('user_id', auth()->id())->get();
            return ApiResponse::success(GroupResource::collection($groups));
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(StoreGroupRequest $request)
    {
        try {
            $validated = $request->validated();

            // Add user_id to the validated data
            $validated['user_id'] = auth()->id();

            $group = Group::create($validated);

            return ApiResponse::success(new GroupResource($group), 'Group created successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong: ' . $e->getMessage());
        }
    }

    public function edit(Group $group)
    {
        // Check group belongs to authenticated user
        if ($group->user_id !== auth()->id()) {
            return ApiResponse::error('Unauthorized access');
        }

        return view('groups.edit', compact('group'));
    }

    public function update(StoreGroupRequest $request, Group $group)
    {
        try {
            // Check group belongs to authenticated user
            if ($group->user_id !== auth()->id()) {
                return ApiResponse::error('Unauthorized access');
            }

            $validated = $request->validated();
            $group->update($validated);

            return ApiResponse::success(new GroupResource($group), 'Group updated successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy(Group $group)
    {
        try {
            // Check group belongs to authenticated user
            if ($group->user_id !== auth()->id()) {
                return ApiResponse::error('Unauthorized access');
            }

            $group->delete();

            return ApiResponse::success([], 'Group deleted successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong: ' . $e->getMessage());
        }
    }
}