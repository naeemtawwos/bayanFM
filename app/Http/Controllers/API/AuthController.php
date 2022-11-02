<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Exception;
use App\Http\Requests\API\UserLoginRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\TokenManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Hashing\HashManager;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;



class AuthController extends Controller
{
    use ThrottlesLogins;

    /** @param User $user */
    public function __construct(
        private UserRepository $userRepository,
        private HashManager $hash,
        private TokenManager $tokenManager,
        private ?Authenticatable $user
    ) {
    }

    public function login(UserLoginRequest $request)
    {
        /** @var User|null $user */
        $user = $this->userRepository->getFirstWhere('email', $request->email);

        if (!$user || !$this->hash->check($request->password, $user->password)) {
            abort(Response::HTTP_UNAUTHORIZED, 'Invalid credentials');
        }

        return response()->json([
            'token' => $this->tokenManager->createToken($user)->plainTextToken,
        ]);
    }

    public function logout()
    {
        $this->user?->currentAccessToken()->delete(); // @phpstan-ignore-line

        return response()->noContent();
    }

    public function register(Request $request)
    {


            if(env('VITE_ALLOW_REGISTRATION') === "0"){
                throw new Exception("This instance does not allow registration", 403);
            }
            $validated = $request->validate([
                'name' => ['required','min:3'],
                'email' => ['required','email', Rule::unique('users', 'email')],
                'password' => ['required','min:6', 'confirmed'],
                // 'password_confirmation' => ['required'],
            ]);

            $validated['password'] = bcrypt($validated['password']);

            $user = User::create($validated);

            //auth()->login($user);

            return response()->json([
                'token' => $this->tokenManager->createToken($user)->plainTextToken,
            ]);
    }

    public function forgotPassword(Request $request){
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status;

        // $status === Password::RESET_LINK_SENT
        //     ? back()->with(['status' => __($status)])
        //     : back()->withErrors(['email' => __($status)]);
        // return response()->json($status);


    }
}
