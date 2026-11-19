<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
 use Illuminate\Support\Facades\Auth;
/**
 * @method static void created(\Closure|string $callback)
 * @method static void updated(\Closure|string $callback)
 * @method static void deleted(\Closure|string $callback)
 * @mixin Model
 */
trait LogsActivity
{
    
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            static::recordActivity($model, 'CREATE', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            $original = array_intersect_key($model->getOriginal(), $changes);

            static::recordActivity($model, 'UPDATE', json_encode($original), json_encode($changes));
        });

        static::deleted(function ($model) {
            static::recordActivity($model, 'DELETE', json_encode($model->getAttributes()), null);
        });
    }

   

protected static function recordActivity($model, $action, $oldValues = null, $newValues = null)
{
    // استخدام Auth Facade يمنع خطأ Undefined method تماماً في IDE
    $userId = Auth::id() ?? session('user_id');

    $modelId = method_exists($model, 'getKey') ? $model->getKey() : ($model->id ?? null);

    ActivityLog::create([
        'user_id'     => $userId,
        'action'      => $action,
        'model_type'  => get_class($model),
        'model_id'    => $modelId,
        'description' => "عملية {$action} على " . class_basename($model),
        'old_values'  => is_string($oldValues) ? $oldValues : json_encode($oldValues),
        'new_values'  => is_string($newValues) ? $newValues : json_encode($newValues),
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
    ]);
}
}