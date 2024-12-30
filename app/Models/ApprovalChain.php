<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalChain extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function steps()
    {
        return $this->hasMany(ApprovalChainStep::class, 'approval_chain_id', 'id')
            ->orderBy('step_order', 'asc');
    }
}
