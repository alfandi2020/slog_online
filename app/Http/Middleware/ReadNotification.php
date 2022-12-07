<?php

namespace App\Http\Middleware;

use Closure;

class ReadNotification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user() && $request->get('source') == 'notif' && $request->has('notif_id')) {
            $notif = $request->user()->unreadNotifications()->where('id', $request->get('notif_id'))->first();
            if (!is_null($notif))
                $notif->markAsRead();
        }
        return $next($request);
    }
}
