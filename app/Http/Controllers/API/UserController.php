<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Mail;
use App\Mail\DemoMail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
    //
    public function register(Request $request): JsonResponse{
      $validator = Validator::make($request->all(), [
        'name' => ['required', 'string', 'min:2', 'max:100'],
        'email' => [
            'required',
            'string',
            'email',
            'min:6',
            'max:100',
            'unique:users',
            function ($attribute, $value, $fail) {
                // Custom validation rule to check if the domain part is present
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $fail($attribute . ' is not a valid email address.');
                }
            },
        ],
        'password' => [
            'required',
            'string',
            'min:6',
            'confirmed',
            'regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
        ],
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    try {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user], JsonResponse::HTTP_CREATED);
    } catch (\Exception $e) {
        // Log the error for debugging purposes
        \Log::error('User registration failed: ' . $e->getMessage());

        return response()->json(['message' => 'User registration failed'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}

public function login(Request $request)
{
    try {
        // Filter input to prevent SQL injection
        $email = filter_var($request->input('email'), FILTER_SANITIZE_EMAIL);
        $password = filter_var($request->input('password'), FILTER_SANITIZE_STRING);

        $validator = Validator::make([
            'email' => $email,
            'password' => $password,
        ], [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $credentials = $validator->validated();

        if (!$token = auth()->attempt($credentials)) {
            throw ValidationException::withMessages(['msg' => 'Email or password is incorrect']);
        }

        return $this->respondWithToken($token);
    } catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    } catch (\Exception $e) {
        // Log the error for debugging purposes
        return response()->json(['errors' => true, 'msg' => 'An unexpected error occurred'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}


protected function respondWithToken($token)
{
    $expiration = auth()->factory()->getTTL() * 60;

    return response()->json([
        'success' => true,
        'access_token' => $token,
        'token_type' => 'Bearer',
        'expires_in' => $expiration,
    ]);
}


//logout
public function logout()
{
    try {
        if (Auth::check()) {
            Auth::logout();
            return response()->json(['success' => true, 'msg' => 'User logged out!']);
        } else {
            // User is not authenticated, indicating token expiration
            return response()->json(['success' => false, 'msg' => 'Token has expired'], JsonResponse::HTTP_UNAUTHORIZED);
        }
    } catch (\Exception $e) {
        // Log the error for debugging purposes
        return response()->json(['success' => false, 'msg' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}

//profile
public function profile(){
    try {
        return response()->json(['success' => true, 'data' => auth()->user()]);
    } catch (\Exception $e) {
        // Log the error for debugging purposes
        return response()->json(['success' => false, 'msg' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}

//update Profile method
public function updateProfile(Request $request)
{
    try {
        if (auth()->user()) {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'name' => 'required|string',
                'email' => 'required|email|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Update user profile logic goes here
            $user = User::find($request->id);

            if (!$user) {
                return response()->json(['success' => false, 'msg' => 'User not found'], 404);
            }

            $user->name = $request->name;
            if($user->email !=$request->email){
               $user->is_verified = 0;
            }
            $user->email = $request->email;
            $user->save();

            return response()->json(['success' => true, 'msg' => 'Profile updated successfully']);
        } else {
            return response()->json(['success' => false, 'msg' => 'User is not authenticated'], 401);
        }
    } catch (\Exception $e) {
        // Log the error for debugging purposes
        return response()->json(['success' => false, 'msg' => 'An unexpected error occurred'], 500);
    }
}

public function sendVerifyMail($email)
{
    $user = User::where('email', $email)->first();
    // echo $user;
    // die;
    if (Auth::check()) {
        $user = User::where('email', $email)->first();

        if ($user) {
            $random = Str::random(40);
            $domain = URL::to('/');
            $url = $domain . '/verify-mail/' . $random;

            $data = [
                'url' => $url,
                'email' => $email,
                'title' => "Email Verification",
                'body' => "Please click the link below to verify your email",
            ];

            // Mail::to('thehaiderali01@gmail.com')->send(new DemoMail($mailData));
            Mail::to($email)->send(new DemoMail($data)); // Use your Mailable class

            //this code is not worked
            // Mail::send('verifymail', ['data'=>$data], function ($message) use($data) {
            //     $message->to($data['email'])->subject($data['title']);
            // });

            $user->remember_token = $random;
            $user->save();

            return response()->json(['success' => true, 'msg' => 'Mail sent successfully']);
        } else {
            return response()->json(['success' => false, 'msg' => 'User not found'], 404);
        }
    } else {
        return response()->json(['success' => false, 'msg' => 'User not authenticated'], 401);
    }
}
public function verificationMail($token){
   $user = User::where('remember_token',$token)->get();
   if(count($user)>0){
          $datetime = Carbon::now()->format('Y-m-d H:i:s');
          $user = User::find($user[0]['id']);
          $user->remember_token='';
          $user->is_verified=1;
          $user->email_verified_at=$datetime;
          $user->save();
          return "<h1>Email verified successfullt</h1>";
   }else{
     return view('404');
   }
}

public function refreshToken()
{
    try {
        if (auth()->user()) {
            return $this->respondWithToken(auth()->refresh());
        } else {
            return response()->json(['success' => false, 'msg' => 'User is Not Authenticated'], 401);
        }
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'msg' => 'An unexpected error occurred'], 500);
    }
}

public function forgotPassword(Request $request)
{
    try {
        $user = User::where('email', $request->email)->first(); // Use first() to get a single user

        if ($user) {
            $token = Str::random(40);
            $domain = URL::to('/');
            $url = $domain.'/reset-password?token='.$token;

            $data = [
                'url' => $url,
                'email' => $request->email, // Use $request->email instead of undefined $email
                'title' => "Password Reset",
                'body' => "Please click the link below to verify your email",
            ];

            Mail::to($request->email)->send(new ResetPasswordMail($data)); // Use $request->email

            $datetime = now(); // Use the now() helper to get the current timestamp
            PasswordReset::updateOrCreate(
                ['email' => $request->email],
                [
                    'email' => $request->email,
                    'token' => $token,
                    'created_at' => $datetime,
                ]
            );

            return response()->json(['success' => true, 'msg' => "Please Check Your Mail to reset your Password"]);
        } else {
            return response()->json(['success' => false, 'msg' => "User not found"]);
        }

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'msg' => $e->getMessage()]);
    }
}
// reset password Load
public function resetPasswordLoad(Request $request)
{
    try {
        if (isset($request->token)) {
            $resetData = PasswordReset::where('token', $request->token)->get();
            if (count($resetData) > 0) {
                $user = User::where('email', $resetData[0]['email'])->get();
                return view('resetPassword', compact('user'));
            }
        }
        return view('404');
    } catch (\Exception $e) {
        // Log the error for debugging purposes
        return response()->json(['success' => false, 'msg' => 'An unexpected error occurred'], 500);
    }
}



public function resetPassword(Request $request)
{
    try {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::find($request->id);
        // dd($user);
        // exit();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();

            PasswordReset::where('email',$user->email)->delete();

            return "<h1>Your password has been reset successfully.</h1>";
        } else {
            return "<h1>User not found.</h1>";
            // return redirect()->route('password.reset.failure')->with('message', 'User not found.');
        }
    } catch (\Exception $e) {
        // Log the error for debugging purposes
        return "<h1>An unexpected error occurred.</h1>";
        // return redirect()->route('password.reset.failure')->with('message', 'An unexpected error occurred.');
    }
}


}
