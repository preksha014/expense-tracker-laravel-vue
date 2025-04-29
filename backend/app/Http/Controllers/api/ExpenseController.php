<?php

namespace App\Http\Controllers\api;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Models\Group;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExpensesExport;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpenseController extends Controller
{
    public function index()
    {
        try {
            $expenses = Expense::where('user_id', auth()->id())->with('group')->get();
            return ApiResponse::success(ExpenseResource::collection($expenses));
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong: ' . $e->getMessage());
        }
    }

    public function create()
    {
        // $groups=Group::all();
        $groups = Group::where('user_id', auth()->id())->get();
        return view('expenses.create', compact('groups'));
    }

    public function store(StoreExpenseRequest $request)
    {
        try {
            $validated = $request->validated();

            // Add user_id to the validated data
            $validated['user_id'] = auth()->id();

            $expense = Expense::create($validated);
            $expense->load('group');

            return ApiResponse::success(new ExpenseResource($expense), 'Expense created successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong: ' . $e->getMessage());
        }
    }

    public function edit(Expense $expense)
    {
        $groups = Group::where('user_id', auth()->id())->get();

        // Check expense belongs to authenticated user
        if ($expense->user_id !== auth()->id()) {
            return ApiResponse::error('Unauthorized access');
        }

        return view('expenses.edit', compact('expense', 'groups'));
    }

    public function update(StoreExpenseRequest $request, Expense $expense)
    {
        try {
            // Check expense belongs to authenticated user
            if ($expense->user_id !== auth()->id()) {
                return ApiResponse::error('Unauthorized access');
            }
            $validated = $request->validated();

            $expense->update($validated);
            $expense->load('group');

            return ApiResponse::success(new ExpenseResource($expense), 'Expense updated successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy(Expense $expense)
    {
        try {
            // Check expense belongs to authenticated user
            if ($expense->user_id !== auth()->id()) {
                return ApiResponse::error('Unauthorized access');
            }
            $expense->delete();

            return ApiResponse::success([], 'Expense deleted successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Something went wrong: ' . $e->getMessage());
        }
    }
    public function exportCSV()
    {
        try {
            // Get the authenticated user's ID
            $userId = auth()->id();

            // Check if user has expenses
            $expenseCount = Expense::where('user_id', $userId)->count();
            if ($expenseCount === 0) {
                return ApiResponse::error('No expenses found for export');
            }

            // Generate a filename with timestamp
            $fileName = 'expenses-' . now()->format('Y-m-d-H-i-s') . '.csv';

            // Return the download response
            return Excel::download(new ExpensesExport($userId), $fileName);
        } catch (\Exception $e) {
            // Log the error
            Log::error('CSV Export failed: ' . $e->getMessage());

            // Return an error message
            return ApiResponse::error('Failed to export expenses: ' . $e->getMessage());
        }
    }

    public function exportPDF()
    {
        try {
            // Get the authenticated user's expenses
            $expenses = Expense::where('user_id', auth()->id())
                ->with('group')
                ->get();

            if ($expenses->isEmpty()) {
                return ApiResponse::error('No expenses found for export');
            }

            // Generate PDF
            $pdf = Pdf::loadView('pdf.expenses', ['expenses' => $expenses]);

            // Generate filename with timestamp
            $fileName = 'expenses-' . now()->format('Y-m-d-H-i-s') . '.pdf';

            // Return the PDF for download
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('PDF Export failed: ' . $e->getMessage());
            return ApiResponse::error('Failed to export PDF: ' . $e->getMessage());
        }
    }
}