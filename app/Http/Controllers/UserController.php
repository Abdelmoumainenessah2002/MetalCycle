<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    return view('users.create');
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // 1. التحقق من البيانات
    $request->validate([
        'name' => 'required|max:100',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
    ]);

    // 2. حفظ المستخدم
    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => $request->password,
    ]);

    // 3. الرجوع إلى قائمة المستخدمين
    return redirect()->route('users.index')
                     ->with('success', 'Utilisateur ajouté avec succès.');
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
    
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
        ]);
    
        $user = User::findOrFail($id);
    
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = $request->password;
        }
        
        $user->save();
    
        return redirect()->route('users.index')
                         ->with('success', 'Utilisateur modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
    
        $user->delete();
    
        return redirect()->route('users.index')
                         ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
