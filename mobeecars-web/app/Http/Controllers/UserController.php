<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('users');
    }

    public function data(Request $request)
    {
        $users = User::select([
            'id',
            'name',
            'email',
            'role',
            'created_at',
        ]);

        return DataTables::of($users)
            // ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y H:i');
            })
            ->addColumn('action', function ($row) {
                $editButton = '
                    <button
                        type="button"
                        class="btn btn-sm btn-success btn-edit"
                        data-id="'.$row->id.'"
                        data-url="'.route('users.update', $row->id).'"
                        style="width: 1.5rem; height: 1.5rem; padding: 0;">
                        <i class="fa fa-edit"></i>
                    </button>
                ';
                $deleteButton = '
                    <button
                        type="button"
                        class="btn btn-sm btn-danger btn-delete"
                        data-id="'.$row->id.'"
                        data-url="'.route('users.destroy', $row->id).'"
                        style="width: 1.5rem; height: 1.5rem; padding: 0;">
                        <i class="fa fa-trash"></i>
                    </button>
                ';
                return '
                    <div class="d-flex gap-2 justify-content-center">
                        '.$editButton.'
                        '.$deleteButton.'
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:3'],
            'confirm_password' => ['required', 'same:password'],
            'name' => ['required', 'max:255'],
            'role' => ['required', 'in:admin,user'],
        ]);

        User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'name' => $validated['name'],
            'role' => $validated['role'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User added successfully.',
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'min:3'],
            'confirm_password' => ['same:password'],
            'name' => ['required', 'max:255'],
            'role' => ['required', 'in:admin,user'],
        ]);

        $updateData = [
            'email' => $validated['email'],
            'name' => $validated['name'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
        ]);
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id == auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ]);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }
}