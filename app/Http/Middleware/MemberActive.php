<?php

namespace App\Http\Middleware;

use App\Member;
use Closure;
use Illuminate\Support\Facades\Auth;

class MemberActive
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
        $member = Auth::user()->member;
        if($member->status == Member::TYPE_INACTIVE)
        {
            return redirect('/member/welcome');
        }

        return $next($request);
    }
}
