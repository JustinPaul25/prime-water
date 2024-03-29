<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\Utility;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;
use Illuminate\Http\Request;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        if($request->user()) {
            $isAdmin = $request->user()->isAdmin();
        } else {
            $isAdmin = false;
        }

        if($request->user()) {
            $isCashier = $request->user()->isCashier();
        } else {
            $isCashier = false;
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? User::where('id', $request->user()->id)->with('account')->first() : $request->user(),
                'admin' => $isAdmin,
                'cashier' => $isCashier,
                'price' => Utility::latest()->first(),
                'price_list' => Utility::orderBy('created_at', 'desc')->get(),
            ],
            'cover_url' => url('/img/coverimg.jpg'),
            'logo' => url('/images/logo/logo.png'),
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
        ]);
    }
}
