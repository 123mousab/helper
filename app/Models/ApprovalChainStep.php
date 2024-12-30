<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalChainStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'approval_chain_id',
        'user_id',
        'step_order',
        'approved',
    ];

    public function approvalChain()
    {
        return $this->belongsTo(ApprovalChain::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    public function scopeNotApproved($query)
    {
        return $query->where('approved', false);
    }
}
