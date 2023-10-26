<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Alert;

class AuthController extends Controller
{
    /*
        @Author : Developer
        @Desc   : Redirect page of login module.
        @Input  : 
        @Output : \Illuminate\Http\Response
        @Date   : 21-Oct-2023
    */

    public function login(): View
    {
        try {
            return view('auth.login');
        } catch(\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::info("This is AuthController login function.");
            Log::error(config('constant.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage().'  '.$e->getLine(),
            ]);
        }
    }

    /*
        @Author : Developer
        @Desc   : Admin Login.
        @Input  : \Illuminate\Http\Request LoginRequest $request
        @Output : \Illuminate\Http\Response
        @Date   : 21-Oct-2023
    */

    public function loginAuth(LoginRequest $request): RedirectResponse
    {
        try { 
            if(!Auth::check()) {
                DB::beginTransaction();

                // Check user email is exists or not
                $user = User::where('email', $request->email)
                            ->first();
                
                if(!empty($user)) {
                    if(Hash::check( $request->password, $user->password )) { 
                        $credentials = $request->only('email', 'password');
                        if (Auth::attempt($credentials)) {
                            return redirect()->route('book.index');
                        } else {
                            DB::rollback();
                            Alert::toast('Invalid email or password.', 'error')->autoClose(5000);
                            return redirect()->route('login');    
                        }
                    } else {
                        DB::rollback();
                        Alert::toast('Invalid email or password.', 'error')->autoClose(5000);
                        return redirect()->route('login');    
                    }
                } else {
                    DB::rollback();
                    Alert::toast('Invalid email or password.', 'error')->autoClose(5000);
                    return redirect()->route('login');
                }
            } else {
                return redirect()->route('book.index');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::info("This is AuthController login save function.");
            Log::error(config('constant.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage().'  '.$e->getLine(),
            ]);   
        }
    }

    /**
        * @Author : Developer
        * @Desc   : Admin session destroy.
        * @Input  : 
        * @Output : \Illuminate\Http\Response
        * @Date   : 22-Oct-2023
    */

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            Session::flush();
            Alert::toast('You are successfully logout.', 'success')->autoclose(3500);
            return redirect()->route('login');
        } catch(\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::info("This is BookController index function.");
            Log::error(Config::get('constant.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage().'  '.$e->getLine(),
            ]);
        }
    }    
}
