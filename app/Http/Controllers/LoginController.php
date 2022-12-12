<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function registrar(Request $request)
    {
        $validacion= validator::make(
            $request->all(),
            [
                "name"=>"required|Max:255",
                "email"=>"required|Max:255",
                "password"=>"required|Max:255",
                "telefono"=>"required",
                "sexo"=>"required|in:femenino,masculino",
            ],
            [
                'name.required' => 'El campo :attribute es obligatorio',
                'name.string' => 'El campo :attribute debe ser de tipo string',
                'name.max' => 'El campo :attribute debe ser de maximo :max caracteres',
                'telefono.required' => 'El campo :attribute es obligatorio',
                'telefono.max' => 'El campo :attribute debe ser de maximo :max caracteres',
                'email.required'  => 'El campo :attribute es obligatorio',
                'email.string' => 'El campo :attribute debe ser de tipo string',
                "password.required"  => 'El campo :attribute es obligatorio',
                
            ]
        );
           
        if($validacion->fails())
        {
            return response()->json(
                [
                    "status"=>400,
                    "mensaje"=>"Validacion no exitosa",
                    "Error"=>$validacion->errors(),
                    "Data"=>[]
                ], 400
                );
        }

        $user = new User();

        $user ->name = $request->name;
        $user ->email = $request->email;
        $user ->password =Hash::make($request->password);
        $user ->telefono = $request->telefono;
        $user ->sexo = $request->sexo;
        $user ->status = $request->status='1';
        
        
        if($user->save())
        {

            /*$response = Http::withHeaders(["api-key"=>"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"])
            ->post("https://api.sendinblue.com/v3/senders",[
                "name"=>$request->name,
                "email"=>$request->email
            ]);
            if($response->successful()){*/
            return response()->json(
                [
                    "status"=>201,
                    "mensaje"=>"Usuario registrado",
                    "error"=>null,
                    "data"=>$user,
                   'user'=>$user,
                ],201
                );
           // }
        }
        else 
        {
            return response()->json(
                [
                    "status"=>400,
                    "mensaje"=>"usuario no creado",
                    "error"=>null,
                    "data"=>[]
                ],400
                );
        }

    }

    public function createlogin(Request $request)
{
    $validacion= validator::make(
        $request->all(),
        [
            "email"=>"required|Max:255",
            "password"=>"required|Max:255",
        ]
    );
    if($validacion->fails())
    {
        return response()->json(
            [
                "status"=>400,
                "mensaje"=>"Validacion no exitosa",
                "Error"=>$validacion->errors(),
                "Data"=>[]
            ], 400
            );
    }

    $user = User::whereEmail($request->email)->first();
    
    if(!is_null($user) && Hash::check($request->password, $user->password))
    {
        if($user->status == false)
        {
            return response()->json(
                [
                    "status"=>400,
                    "mensaje"=>"cuenta desactivada, favor de activarlo mediante el correo que recibio",
                ],400
                );
        }

        if($user->save())
       {
          $token=$user->CreateToken("Token")->plainTextToken;
         return response()->json(
            [
             "status"=>201,
             "id"=>$user->id,
             "mensaje"=>"Bienvenido al sistema",
             "error"=>null,
             "data"=>$user,
             "token"=>$token
            ],201
         );
         
        }
    }
    else{

        return response()->json(
            [
                "status"=>400,
                "mensaje"=>"Los datos no son correctos",
                "error"=>null,
                "data"=>$user,
            ],400
            );
    }
}

public function logout(Request $request)
    {
        $user=$request->user();
        
    return response()->json(
        [
            "status"=>201,
            "mensaje"=>"Se ha cerrado exitosamente",
            "error"=>null,
            "token"=>$request->user()->tokens()->delete(),
        ],201
        );    
    }

    public function verificarUsuario(Request $request){

        if(!$request->hasValidSignature())
        abort(401);

        $user= User::all()->last();

        $response = Http::get("http://127.0.0.1:9000/api/v4/enviar/mensaje");

        if($response->successful()){
            return response()->json(
                [
                    "status"=>200,
                    "mensaje"=>"Mensaje Enviado",
                    "error"=>null,
                    "data"=>[],
                ],200);

        }

    }

    public function verificarSms(Request $request){

        $validacion= validator::make(
            $request->all(),
            [
                "codigo"=>"required",
                "request_id"=>"required",
                "email"=>"required"
            ]
        );
        if($validacion->fails())
        {
            return response()->json(
                [
                    "status"=>400,
                    "mensaje"=>"Validacion no exitosa",
                    "Error"=>$validacion->errors(),
                    "Data"=>[]
                ], 400
                );
        }

        $response = Http::get("https://api.nexmo.com/verify/check/json?&api_key=89ca7390&api_secret=sFI3tYAjqElnKZGf&request_id=$request->request_id&code=$request->codigo");

        if($response->successful()){
            $User = User::whereEmail($request->email)->first();
            if(!is_null($User)){

                $User->active=1;
                if($User->save()){
                    return response()->json(
                        [
                            "status"=>200,
                            "mensaje"=>"Validacion exitosa",
                            "Error"=>[],
                            "Data"=>[]
                        ], 200
                        );

                }
                
            }
        }

    }



    public function activarCuenta(Request $request)
    {
        $validacion= validator::make(
            $request->all(),
            [
                "email"=>"required|Max:255",
                "password"=>"required|Max:255"
            ]
        );
        if($validacion->fails())
        {
            return response()->json(
                [
                    "status"=>400,
                    "mensaje"=>"Validacion no exitosa",
                    "Error"=>$validacion->errors(),
                    "Data"=>[]
                ], 400
                );
        }
    
        $User = new User();
        $User = User::whereEmail($request->email)->first();
        if(!is_null($User) && Hash::check($request->password, $User->password))
        {
            if($User->save())
            {
                $response=Http::post("http://127.0.0.1:9000/api/v4/job/mailCuenta",[
                    "User"=>$User->id,
                    "Url"=>URL::signedRoute('validacion',['user'=>$User])
                ]);

                if($response->successful()){
                    return response()->json(
                        [
                            "status"=>201,
                            "mensaje"=>"Los datos se insertaron de manera correcta",
                            "error"=>null,
                            "data"=>$User,
                        ],201
                        );

                }
                return response()->json(
                    [
                        "status"=>400,
                        "mensaje"=>"Mal",
                        "error"=>null,
                        "data"=>$User,
                    ],400
                    );

            }
    
        }
    
        return response()->json(
            [
                "status"=>400,
                "mensaje"=>"Los datos no son correctos",
                "error"=>null,
                "data"=>$User,
            ],400
            );
    }
}