<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use App\Traits\IntegrationsTrait;
use App\Traits\MyCrypto;


class MtnCgController extends Controller
{
    use IntegrationsTrait;
    use MyCrypto;
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function keepTokenAlive(Request $request)
    {
        $data = $this->KeepCookieAlive();

        return view("mtn_cg_website.account_active_cg",['final_cookies' => $data]);
    }


   

}
