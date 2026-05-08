<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTimesheetEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'timesheet_id' => 'required|exists:timesheets,id',
            'date' => 'required|date',
            'check_in' => 'nullable|string',
            'check_out' => 'nullable|string',
            'break_duration' => 'nullable|integer',
            'total_hours' => 'nullable|numeric',
            'planned_hours' => 'nullable|numeric',
            'overtime_hours' => 'nullable|numeric',
            'absence_type' => 'nullable|string',
            'comment' => 'nullable|string',
        ];
    }
}
