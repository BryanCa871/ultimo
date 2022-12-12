<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class AdafruitController extends Controller
{
    public function humedad (){
        $response = Http::get("https://io.adafruit.com/api/v2/Abel0120/feeds/default-dot-humedad?X-AIO-KEY=aio_nFdz91zKSQCuOpVqj3rnGwU7XaS0",[]);

        return response()->json([
            "status"=>201,
            "message"=>"Correcto",
            "error"=>null,
            "sensores"=>$response->object()->id,
           "datas"=>$response->object()->last_value,
            ],201);

            


        }  


        
        public function luz (){
            $response = Http::get("https://io.adafruit.com/api/v2/Abel0120/feeds/default-dot-luz?X-AIO-KEY=aio_nFdz91zKSQCuOpVqj3rnGwU7XaS0",[]);
    
            return response()->json([
                "status"=>201,
                "message"=>"Correcto",
                "error"=>null,
                "sensores"=>$response->object()->id,
               "datas"=>$response->object()->last_value,
                ],201);
            } 

            public function corrientemax(){
                $response = Http::get("https://io.adafruit.com/api/v2/Abel0120/feeds/default-dot-imax?X-AIO-KEY=aio_nFdz91zKSQCuOpVqj3rnGwU7XaS0",[]);
        
                return response()->json([
                    "status"=>201,
                    "message"=>"Correcto",
                    "error"=>null,
                    "sensores"=>$response->object()->id,
                   "datas"=>$response->object()->last_value,
                    ],201);
                } 

                public function corrientemin (){
                    $response = Http::get("https://io.adafruit.com/api/v2/Abel0120/feeds/default-dot-imin?X-AIO-KEY=aio_nFdz91zKSQCuOpVqj3rnGwU7XaS0",[]);
            
                    return response()->json([
                        "status"=>201,
                        "message"=>"Correcto",
                        "error"=>null,
                        "sensores"=>$response->object()->id,
                       "datas"=>$response->object()->last_value,
                        ],201);
                    } 

                    public function corrientewatts (){
                        $response = Http::get("https://io.adafruit.com/api/v2/Abel0120/feeds/default-dot-watts?X-AIO-KEY=aio_nFdz91zKSQCuOpVqj3rnGwU7XaS0",[]);
                
                        return response()->json([
                            "status"=>201,
                            "message"=>"Correcto",
                            "error"=>null,
                            "sensores"=>$response->object()->id,
                           "datas"=>$response->object()->last_value,
                            ],201);
                        } 

                        public function temperatura (){
                            $response = Http::get("https://io.adafruit.com/api/v2/Abel0120/feeds/default-dot-temperatura?X-AIO-KEY=aio_nFdz91zKSQCuOpVqj3rnGwU7XaS0",[]);
                    
                            return response()->json([
                                "status"=>201,
                                "message"=>"Correcto",
                                "error"=>null,
                                "sensores"=>$response->object()->id,
                               "datas"=>$response->object()->last_value,
                                ],201);
                            } 

                 
                            
                                public function si(Request $request){
                                    $validacion = validator::make(
                                        $request->all(),
                                             [
                                                    "nombre"=>"required|Max:255",
                                            ]);
                                        if($validacion->fails())
                                            {
                                                return response()->json(
                                                    [
                                                        "status"=>400,
                                                        "mensaje"=>"validacion no exitosa",
                                                        "Error"=>$validacion->errors(),
                                                        "Data"=>[]
                                                        ],400);
                                            }
                                
                                            $response= Http::post("https://io.adafruit.com/api/v2/Abel0120/groups?x-aio-key=aio_CAwM13gzVIEUhoVIhinQLPpezF0d",[
                                                "name"=>$request->nombre,
                                            ]);
                                
                                            if($response->successful()){
                                                $feeds = array("Humo","Luz","Magnetico","Humedad","Temperatura");
                                
                                                for($i=0; $i<=4; $i++){
                                                    $response2 = Http::post("https://io.adafruit.com/api/v2/Abel0120/groups/".$request->nombre."/feeds?x-aio-key=aio_CAwM13gzVIEUhoVIhinQLPpezF0d",[
                                                        "name"=>$feeds[$i],
                                                    ]);
                                                }
                                
                                                if($response2->successful()){
                                                    $response3=Http::get("https://io.adafruit.com/api/v2/Abel0120/groups/".$request->nombre."?x-aio-key=aio_CAwM13gzVIEUhoVIhinQLPpezF0d");
                                                    return response()->json([
                                                        "status"=>200,
                                                        "message"=>"feed creada",
                                                        "error"=>null,
                                                        "data"=>$response3->json()
                                                    ],200);
                                                }
                                            }
                                
                                        }
                                    }
