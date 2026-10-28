<?php

namespace App\Http\Controllers;

use App\Services\MlService;
use Illuminate\View\View;

class ModelController extends Controller
{
    public function index(MlService $ml): View
    {
        $payload = $ml->metrics();

        return view('model.index', [
            'metrics' => $payload['data'],
            'source' => $payload['source'],
        ]);
    }
}
