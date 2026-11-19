<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. التحقق من وجود الجلسة المخصصة
        if (!session()->has('user_id')) {
            // إذا كان الطلب AJAX وانتهت الجلسة، نرجع 401 ليقوم الـ JS بإعادة التحميل بنظافة
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            return redirect()
                ->route('login')
                ->with('error', 'Veuillez vous connecter.');
        }

        // 2. تسجيل دخول المستخدم في الذاكرة للطلب الحالي فقط دون إعادة كتابة الجلسة على القرص
        if (!Auth::check()) {
            Auth::onceUsingId(session('user_id'));
        }

        return $next($request);
    }
}