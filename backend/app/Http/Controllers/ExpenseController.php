<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Group;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('group')->get();
        return response()->json($expenses);
        // return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $groups = Group::all();
        return view('expenses.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated=$request->validate([
            'name' => 'required|string',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'group_id' => 'required|exists:groups,id',
          ]);          

        $expense=Expense::create($validated);
        $data = Expense::with('group')->find($expense->id);

        return response()->json([
            'message' => 'Expense created successfully',
            'expense' => $data
        ], 200);
        // return redirect()->route('expenses.index')
        //     ->with('success', 'Expense created successfully.');
    }

    public function edit(Expense $expense)
    {
        $groups = Group::all();
        return view('expenses.edit', compact('expense', 'groups'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'group_id' => 'required|exists:groups,id'
        ]);

        $expense->update($validated);
        $expense = Expense::with('group')->find($expense->id);

        return response()->json([
            'message' => 'Expense updated successfully',
            'expense' => $expense
        ], 200);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return response()->json([
            'message' => 'Expense deleted successfully'
        ], 200);
        // return redirect()->route('expenses.index')
        //     ->with('success', 'Expense deleted successfully.');
    }
}