<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class ExpensesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $userId;

    public function __construct($userId = null)
    {
        $this->userId = $userId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Get expenses for the specific user
        $userId = $this->userId ?? Auth::id();
        return Expense::where('user_id', $userId)->with('group')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Amount',
            'Date',
            'Group',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @param Expense $expense
     * @return array
     */
    public function map($expense): array
    {
        return [
            $expense->id,
            $expense->name,
            $expense->amount,
            $expense->date,
            $expense->group ? $expense->group->name : 'No Group',
            $expense->created_at->format('Y-m-d H:i:s'),
            $expense->updated_at->format('Y-m-d H:i:s')
        ];
    }
}