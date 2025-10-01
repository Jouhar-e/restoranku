<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::whereHas('role', function ($query) {
            $query->where('role_name', '!=', 'customer');
        })->orderBy('fullname', 'asc')->get();

        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::where('role_name', '!=', 'customer')->get();

        return view('admin.user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
        ]);

        $validate['password'] = bcrypt($validate['password']);

        User::create($validate);

        return redirect()->route('users.index')->with('success', 'Data berhasil ditambahkan');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);

        $roles = Role::all();

        return view('admin.user.edit', compact('roles', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'phone' => 'required|string|max:15',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => ['nullable', 'string', 'min:8', 'confirmed', function ($attribute, $value, $fail) use ($user) {
                if (Hash::check($value, $user->password)) {
                    $fail('Password baru tidak boleh sama dengan password lama');
                }
            },
            ],
            'role_id' => 'required|exists:roles,id',
        ], [
            'fullname.required' => 'The full name is required.',
            'username.required' => 'The username is required.',
            'phone.required' => 'The phone number is required.',
            'email.required' => 'The email address is required.',
            'role_id.required' => 'The role is required.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        // Create a new user
        $validatedData['password'] = bcrypt($validatedData['password']);

        $user->update($validatedData);

        // Redirect to the users index with a success message
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }
}
