<?php

namespace App\Http\Middleware;

use App\Entities\Networks\Network;
use App\Entities\Users\Role as UserRole;
use App\Entities\Users\User;
use Closure;
use Illuminate\Support\Arr;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $keys)
    {
        $keys = explode('|', $keys);
        $roles = Arr::only(UserRole::toArray(), $keys);

        if (auth()->check() == false)
            return redirect()->guest('/');

        // TEMPORARY
        view()->share('isSignedIn', auth()->check());
        view()->share('user', auth()->user() ?: new User);
        view()->share('userNetwork', auth()->user() ? auth()->user()->network : new Network);
        // End of TEMPORARY

        // Cek apakah grup user ada di dalam array $nameArray?
        if (array_key_exists(auth()->user()->role_id, $roles))
            return $next($request);

        flash('Anda tidak dapat mengakses halaman ' . $request->path() . '.', 'danger');
        return redirect()->home();
    }
}
