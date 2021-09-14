<?php

namespace App\Http\Controllers;

use App\Kyc\KycSystems;
use Illuminate\Http\Request;

class CloudController extends Controller
{

    private $kycService;
    /**
     * @var array
     */
    private $requestParams;

    public function __construct(KycSystems $service, Request $request)
    {
        $this->kycService = $service;
        $this->requestParams = $request->json()->all();
    }

    public function identify(){
        $iin = $this->requestParams['iin'];
        
        return $this->kycService->verify($this->requestParams, $iin);
    }
}