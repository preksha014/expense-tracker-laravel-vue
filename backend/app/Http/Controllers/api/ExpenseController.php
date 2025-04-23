<?php

namespace App\Http\Controllers\api;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use App\Models\Group;
use App\Http\Controllers\Controller;

class ExpenseController extends Controller
{
    public function index()
    {
        try {
            $expenses = Expense::with('group')->get();
            return ApiResponse::success($expenses);
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $groups = Group::all();
        return view('expenses.create', compact('groups'));
    }

    public function store(StoreExpenseRequest $request)
    {
        try {
            $validated = $request->validated();

            $expense = Expense::create($validated);
            $data = Expense::with('group')->find($expense->id);

            return ApiResponse::success($data, 'Expense created successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong: ' . $e->getMessage());
        }
    }

    public function edit(Expense $expense)
    {
        $groups = Group::all();
        return view('expenses.edit', compact('expense', 'groups'));
    }

    public function update(StoreExpenseRequest $request, Expense $expense)
    {
        try {
            $validated = $request->validated();

            $expense->update($validated);
            $expense = Expense::with('group')->find($expense->id);

            return ApiResponse::success($expense, 'Expense updated successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy(Expense $expense)
    {
        try {
            $expense->delete();

            return ApiResponse::success([], 'Expense deleted successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong: ' . $e->getMessage());
        }
    }
}