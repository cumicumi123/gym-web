<?php

namespace App\Http\Controllers;

use App\Services\MlService;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(MlService $ml): View
    {
        $metrics = $ml->metrics();

        return view('home', [
            'metrics' => $metrics['data'],
            'metricsSource' => $metrics['source'],
            'profile' => auth()->user()?->activeProgram(),
        ]);
    }

    public function about(MlService $ml): View
    {
        $metrics = $ml->metrics();

        return view('tentang', [
            'metrics' => $metrics['data'],
            'metricsSource' => $metrics['source'],
        ]);
    }

    public function privacy(): View
    {
        return view('privasi');
    }
}
