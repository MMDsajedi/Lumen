<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    
    public function signup(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'username' => 'required|string|max:50',
                'email' => 'required|max:50',
                'phone_number' => 'required|string|max:11',
                'password' => 'required|string|min:8',
                'rePassword' => 'required|string|min:8',
            ],
            [
                'first_name.required' => 'فیلد اجباری است',
                'first_name.string' => 'فقط اعداد و حروف  کاراکتر های مجاز استفاده شود',
                'first_name.max' => 'حداکثر رعایت شود',
                'last_name.required' => 'فیلد اجباری است',
                'last_name.string' => 'فقط اعداد و حروف  کاراکتر های مجاز استفاده شود',
                'last_name.max' => 'حداکثر رعایت شود',
                'username.required' => 'فیلد اجباری است',
                'username.string' => 'فقط اعداد و حروف  کاراکتر های مجاز استفاده شود',
                'username.max' => 'حداکثر رعایت شود',
                'email.required' => 'فیلد اجباری است',
                'email.max' => 'حداکثر رعایت شود',
                'phone_number.required' => 'فیلد اجباری است',
                'phone_number.string' => 'فقط اعداد و حروف  کاراکتر های مجاز استفاده شود',
                'phone_number.max' => 'شماره تلفن معتبر نیست',
                'password.required' => 'فیلد اجباری است',
                'password.string' => 'فقط اعداد و حروف  کاراکتر های مجاز استفاده شود',
                'password.min' => 'حداقل ۸ نویسه وارد شود',
                'rePassword.required' => 'فیلد اجباری است',
                'rePassword.string' => 'فقط اعداد و حروف  کاراکتر های مجاز استفاده شود',
                'rePassword.min' => 'حداقل ۸ نویسه وارد شود',
            ]
        );
        if ($validate->fails()) {
            return [
                "status" => false,
                'snackbar' => [
                    'type' => "error",
                    'message' => $validate->errors()->first(),
                ],
            ];
        }

        if($request->password != $request->rePassword){
            return [
                "status" => false,
                'snackbar' => [
                    'type' => "error",
                    'message' => $validate->errors()->first(),
                ],
            ];
        }


        $user = User::create([
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "username" => $request->username,
            "email" => $request->email,
            "phone_number" => $request->phone_number,
            "password" => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'username' => 'required|string|max:50',
                'password' => 'required|string|min:8',
            ],
            [
                'username.required' => 'فیلد نام کاربری اجباری است',
                'username.string' => 'نام کاربری باید شامل حروف یا اعداد باشد',
                'username.max' => 'حداکثر ۵۰ کاراکتر مجاز است',
                'password.required' => 'فیلد کلمه عبور اجباری است',
                'password.string' => 'کلمه عبور باید شامل حروف یا اعداد باشد',
                'password.min' => 'حداقل ۸ کاراکتر وارد کنید',
            ]
        );
    
        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validate->errors()->first(),
            ], 422);
        }
    
        $user = User::where('username', $request->username)->first();
    
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'نام کاربری یافت نشد',
            ], 404);
        }


    
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'کلمه عبور اشتباه است',
            ], 401);
        }
 
        

        try {
            $token = self::generateToken(64);
            
            $user->update([
            'token'=> $token
            ]);
            $user->save();
            dd($user);
        } catch (\Throwable $th) {
             return response()->json([
            'status' => false,
            'message' => 'ورود موفقیت‌آمیز نبود',
        ], 200);
    } 
        

        return response()->json([
            'status' => true,
            'message' => 'ورود موفقیت‌آمیز بود',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'username' => $user->username,
                'email' => $user->email,
                'token' => $token,
            ],
        ], 200);
    }
    
    static function generateToken($length)
    {
        do {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $token = '';
            for ($i = 0; $i < $length; $i++) {
                $token .= $characters[random_int(0, $charactersLength - 1)];
            }
        } while (User::where("token", $token)->count() > 0);

        return $token;

    }

    public function index(Request $request)
    {
        $user = $request->auth_user;
    

        dd($request->user());
        return response()->json([
            'status' => true,
            'message' => 'توکن معتبر است.',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'username' => $user->username,
                'email' => $user->email,
            ],
        ]);
    }
    
    public function store (Request $request){
        dd($request->all());
    }
    public function show (){
        dd("show");
    }
    public function update (){
        dd("update");
    }
    public function destroy (){
        dd("destroy");
    }
    public function new (){
        dd("new");
    }
}
