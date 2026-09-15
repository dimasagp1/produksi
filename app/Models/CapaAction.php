<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapaAction extends Model
{
    use HasFactory;

    protected $table = 'capa_actions';

    protected $fillable = [
        'code',
        'issue_date',
        'source',
        'problem_description',
        'root_cause',
        'corrective_action',
        'preventive_action',
        'pic',
        'due_date',
        'status'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
    ];
}
