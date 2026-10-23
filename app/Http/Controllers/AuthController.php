<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller {
    public function login(LoginRequest $request){$data=$request->validated();$user=User::where('email',$data['email'])->first();if(!$user||!Hash::check($data['password'],$user->password))throw ValidationException::withMessages(['email'=>['The provided credentials are incorrect.']]);return response()->json(['data'=>['token'=>$user->createToken('web')->plainTextToken,'user'=>$user]]);}
    public function register(RegisterRequest $request){$data=$request->validated();$user=User::create([...$data,'role'=>'user']);return response()->json(['data'=>['token'=>$user->createToken('web')->plainTextToken,'user'=>$user]],201);}
    public function me(Request $request){return response()->json(['data'=>$request->user()]);}
    public function logout(Request $request){$request->user()->currentAccessToken()?->delete();return response()->json(['data'=>['message'=>'Logged out']]);}
}
