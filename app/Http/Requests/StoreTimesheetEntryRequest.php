<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTimesheetEntryRequest extends FormRequest
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
        'timesheet_id'   => 'required|exists:timesheets,id', 
        'date'           => 'required|date', 
        
        // Force le format HH:mm (ex: 08:30)
        'check_in'       => 'nullable|date_format:H:i', 
        
        // Force le format HH:mm et vérifie que c'est après l'heure d'arrivée
        'check_out'      => 'nullable|date_format:H:i|after:check_in', 
        
        'break_duration' => 'nullable|integer|min:0', 
        'total_hours'    => 'nullable|numeric', 
        'planned_hours'  => 'nullable|numeric', 
        'overtime_hours' => 'nullable|numeric', 
        'absence_type'   => 'nullable|string|max:255', 
        'comment'        => 'nullable|string|max:500', 
    ]; 
}
}
