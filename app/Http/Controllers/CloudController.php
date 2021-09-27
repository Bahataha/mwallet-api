<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Kyc\KycSystems;
use GuzzleHttp\Psr7;
use thiagoalessio\TesseractOCR\TesseractOCR;

class CloudController extends Controller
{
    private $kycService;

    private $vision;

    public function __construct(KycSystems $service, TesseractOCR $vision){
        $this->kycService = $service;
        $this->vision = $vision;
    }

    public function index(Request $request)
    {
        if($request->hasFile('image')){
            $photoDoc = $request->image;
            $file = $request->file('image');
            $fileName = str_random(40) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads');
            $file->move($destinationPath, $fileName);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'фотография не были переданы'
            ]);
        }

        $path = Storage::disk('public')->path('') . $fileName;

        return $this->vision->image($path)->allowlist(range(0, 9))->run();

        $result = $this->vision->annotate($image);

        foreach($result->info()['textAnnotations'] as $key => $item){
            if(strlen($item['description']) == 12){
                $item['description'] = intval($item['description']);
                if(strlen((string)$item['description']) == 12){
                    $iin = $item['description'];
                }
            }
        }
        if(isset($iin)){
            if(!$this->testIIN($iin)){
                return response()->json([
                    "status" => false,
                    "message" => "Не правильный ИИН"
                ]);
            }
        }
        else{
            return response()->json([
                "status" => false,
                "message" => "ИИН не найден"
            ]);
        }


        return $this->kycService->verify(['image' => $path], $iin);
    }

    public function testIIN($iin){
        $strIIN = (string) $iin;
        $mass = [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11 ];
        $mass2 = [ 3, 4, 5, 6, 7, 8, 9, 10, 11, 1, 2 ];
        $control = 0;
        for($i=0; $i<11; $i++){
            $control += $strIIN[$i] * $mass[$i];
        }
        $control = $control % 11;
        if($control == 10){
            $control = 0;
            for($i=0; $i<11; $i++){
                $control += $strIIN[$i] * $mass2[$i];
            }
            $control = $control % 11;
        }

        return $control == $strIIN[11] ? 1 : 0;
    }
}
