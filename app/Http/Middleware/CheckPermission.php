<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $action)
    {
        if (!Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login');
        }

        $controller = $request->route()->getController();

        // 👈 1. التحقق أولاً إذا كان الكنترولر يحتوي على دالة ديناميكية getScreenCode()
        if (is_object($controller) && method_exists($controller, 'getScreenCode')) {
            $page = $controller->getScreenCode($request);
        }
        // 👈 2. إذا لم توجد الدالة، الاعتماد على الثابت SCREEN_CODE التقليدي
        elseif (is_object($controller) && defined(get_class($controller) . '::SCREEN_CODE')) {
            $page = $controller::SCREEN_CODE;
        } 
        else {
            abort(500, "Le SCREEN_CODE n'est pas défini dans le Controller.");
        }

        // 3. التحقق من الصلاحية
        if (!Auth::user()->canAccess($page, $action)) {
            abort(403, "Accès refusé ! Vous n'avez pas la permission [$action] sur cette page.");
        }

        return $next($request);
    }
}