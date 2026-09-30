<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use HasFactory;

    protected $table = 'payroll';

    protected $fillable = [
        'employee_id',
        'pay_period_start',
        'pay_period_end',
        'basic_salary',
        'allowances',
        'overtime',
        'deductions',
        'net_salary',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'pay_period_start' => 'date',
            'pay_period_end' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Calculate Net Salary: Basic Salary + Allowances + Overtime - Deductions
     */
    public static function calculateNetSalary(float $basic, float $allowances, float $overtime, float $deductions): float
    {
        return max(0, $basic + $allowances + $overtime - $deductions);
    }
}
