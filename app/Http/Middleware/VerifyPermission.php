<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use jeremykenedy\LaravelRoles\App\Exceptions\PermissionDeniedException;

class VerifyPermission
{
    /**
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request    $request
     * @param \Closure   $next
     * @param int|string $permission
     *
     * @throws \jeremykenedy\LaravelRoles\App\Exceptions\PermissionDeniedException
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$permission)
    {

        $permissions_arr =$this->auth?->user()?->permissions_array?->toArray()??[];
        $roles =$this->auth?->user()?->roles_array??[];
        $roles_persmissins = collect();

        //get roles permissions
        $roles?->get()?->each(function ($per)use($roles_persmissins){
            $roles_persmissins->push($per?->permissions?->pluck('slug'));
        });


        if($roles_persmissins->count() > 0){
            $roles_persmissins = $roles_persmissins->flatten()?->unique()?->toArray();
        }

        $foundRoles         = count(array_intersect($permission, $roles_persmissins));
        $foundPermissions   = count(array_intersect($permission, $permissions_arr));


        //if ($this->auth->check() && $this->auth->user()->hasPermission($permission)) {
        if ($this->auth->check() && ($foundRoles > 0 || $foundPermissions > 0)) {
            return $next($request);
        }

        $permission = join(',', $permission);
        throw new PermissionDeniedException($permission);
    }
}
