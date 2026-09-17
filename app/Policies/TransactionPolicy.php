<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return $user->role === 'superadmin' || $transaction->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'superadmin']);
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->role === 'superadmin' || $transaction->created_by === $user->id;
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->role === 'superadmin' || $transaction->created_by === $user->id;
    }

    public function restore(User $user, Transaction $transaction): bool
    {
        return $user->role === 'superadmin';
    }

    public function forceDelete(User $user, Transaction $transaction): bool
    {
        return $user->role === 'superadmin';
    }
}
