<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use App\KaryawanShiftChange;
use App\KaryawanShift;

use Closure;

class cekRole
{
    
    public function handle($request, Closure $next, $role)
    {
        $dateNow = date('Y-m-d');
        $changeShift = KaryawanShiftChange::where('tgl_update', '<=', $dateNow)->get();
        foreach($changeShift as $d) {
            KaryawanShift::where(['karyawan_id' => $d->karyawan_id])->update([
                'shift_id'  => $d->shift_id
            ]);

            KaryawanShiftChange::destroy($d->id);
        }

        if(Auth::guard(null)->check())
        {
            $auth = Auth::user()->role;
            if($auth == $role or $auth == 'admin');
            {
                return $next($request);
            }
        }

        return redirect('/login');
    }
}
