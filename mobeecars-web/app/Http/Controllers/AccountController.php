<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = User::findOrFail(auth()->id());
        return view('account', compact('user'));
    }

    public function update(Request $request, string $id)
    {
        if ($id != auth()->id()) {
            return redirect()->route('account.index')->with('error', 'Please update your own account!');
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'min:3'],
            'confirm_password' => ['same:password'],
            'name' => ['required', 'max:255'],
        ]);

        $updateData = [
            'email' => $validated['email'],
            'name' => $validated['name'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('account.index')->with('success', 'Account updated successfully!');
    }
}