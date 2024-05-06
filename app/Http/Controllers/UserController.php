<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = User::query();

            return DataTables::of($query)
                ->addColumn('action', function ($user) {
                    return '
                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editUserModal' . $user->id . '">
                        Edit
                    </button>
                    <form action="' . route('user.destroy', $user->id) . '" method="POST" class="d-inline">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger btn-sm delete-user" data-user-id="' . $user->id . '">Delete</button>
                    </form>
                ';
                })
                ->rawColumns(['action'])
                ->make();
        }

        $users = User::all(); // Menyediakan data pengguna untuk view
        return view('pages.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'roles' => 'required|string|in:admin,staff,owner,user'
        ]);

        if ($validator->fails()) {
            // Mengambil pesan kesalahan pertama dari validator
            $errorMessage = $validator->errors()->first();

            // Redirect kembali ke halaman indeks dengan pesan kesalahan
            return redirect()->route('user.index')->withErrors($errorMessage);
        }

        try {
            // Menghash password sebelum menyimpannya menggunakan Hash::
            $hashedPassword = Hash::make($request->password);

            // Buat pengguna baru dengan password yang dihash
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $hashedPassword,
                'roles' => $request->roles
            ]);

            // Jika berhasil, kembalikan respons sukses
            return redirect()->route('user.index')->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, redirect kembali ke halaman indeks dengan pesan kesalahan
            return redirect()->route('user.index')->withErrors($e->getMessage());
        }
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'roles' => 'required|string|in:admin,staff,owner,user'
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.index')->with('error', 'User update failed');
        }

        // Hapus password dari request jika tidak diisi
        if (!$request->filled('password')) {
            $request->request->remove('password');
        }

        $user->update($request->all());

        return redirect()->route('user.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        // Redirect back to user index page with success message
        return redirect()->route('user.index')->with('success', 'User deleted successfully!');
    }
}
