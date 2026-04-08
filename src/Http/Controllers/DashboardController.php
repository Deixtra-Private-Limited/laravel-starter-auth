<?php

declare(strict_types=1);

namespace Deixtra\LaravelStarterAuth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        return view(
            config('auth-starter.dashboard_view', 'auth-starter::dashboard'),
            ['user' => $request->user()]
        );
    }
}
