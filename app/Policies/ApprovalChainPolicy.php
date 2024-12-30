<?php

namespace App\Policies;

use App\Models\ApprovalChain;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApprovalChainPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->can('List Approval Chain');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ApprovalChain  $approvalChain
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ApprovalChain $approvalChain)
    {
        return $user->can('View Approval Chain');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('Create Approval Chain');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ApprovalChain  $approvalChain
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ApprovalChain $approvalChain)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ApprovalChain  $approvalChain
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ApprovalChain $approvalChain)
    {
        return $user->can('Delete Approval Chain');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ApprovalChain  $approvalChain
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ApprovalChain $approvalChain)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ApprovalChain  $approvalChain
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ApprovalChain $approvalChain)
    {
        //
    }
}
