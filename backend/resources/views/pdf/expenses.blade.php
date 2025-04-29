<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Expenses Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .expenses-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .expenses-table th, .expenses-table td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
        }
        .expenses-table th { 
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .total { 
            margin-top: 20px; 
            text-align: right; 
            font-weight: bold; 
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Expenses Report</h1>
        <p>Generated on: {{ date('F d, Y') }}</p>
    </div>

    <table class="expenses-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Group</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td>{{ $expense->name }}</td>
                <td>${{ number_format($expense->amount, 2) }}</td>
                <td>{{ $expense->date }}</td>
                <td>{{ $expense->group->name ?? 'No Group' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total: ${{ number_format($expenses->sum('amount'), 2) }}
    </div>
</body>
</html>