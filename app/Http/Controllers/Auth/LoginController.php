<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Models\ActivityLog;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }
   
    public function login(Request $request)
{
    $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    $utilisateur = Utilisateur::with(['person.role'])
        ->where('username', $request->username)
        ->where('actif', 1)
        ->first();

    if (!$utilisateur) {
        return back()->with('error', 'Nom d’utilisateur incorrect.');
    }

    if (!Hash::check($request->password, $utilisateur->password_hash)) {
        return back()->with('error', 'Mot de passe incorrect.');
    }
    // if ($request->password !== $utilisateur->password_hash) {
    //     return back()->with('error', 'Mot de passe incorrect.');
    // }

    $utilisateur->dernier_login = now();
    $utilisateur->saveQuietly();

    session([
        'user_id' => $utilisateur->id,
        'username' => $utilisateur->username,
        'person_id' => $utilisateur->person->id,
        'nom' => $utilisateur->person->nom,
        'prenom' => $utilisateur->person->prenom,
        'role' => $utilisateur->person->role->nom,
    ]);

    // 👈 تسجيل حركة الدخول الناجحة
        ActivityLog::create([
            'user_id'     => $utilisateur->id,
            'action'      => 'LOGIN',
            'description' => 'Connexion réussie',
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

    return redirect('/dashboard');
}

public function logout(Request $request)
{
    $userId = session('user_id');

        // 👈 تسجيل حركة الخروج قبل مسح السيشن
        if ($userId) {
            ActivityLog::create([
                'user_id'     => $userId,
                'action'      => 'LOGOUT',
                'description' => 'Déconnexion du système',
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        }

    $request->session()->flush();

    return redirect('/login');
}


}
