<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Kyc\KycSystems;

class VeriLiveController extends Controller
{
    private $kycService;

    public function __construct(KycSystems $service){
        $this->kycService = $service;
    }

    public function index(Request $request){
        if (!$handle = fopen(public_path('verilive/'). str_random(40) . '.json', 'a')) {
            return "Не могу открыть файл ($filename)";
        }

        if (fwrite($handle, json_encode($request->all())) === FALSE) {
            return "Не могу произвести запись в файл ($filename)";
        }

        fclose($handle);

        return response()->json([
            'status' => true, 
            'message' => 'ok'
        ]);
    }
}
