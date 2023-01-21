<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\ResetPasswordFormRequest;
    use Illuminate\Auth\Events\PasswordReset;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\View\View;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Support\Facades\Password;
    use Illuminate\View\Factory;

    use function view;

    class ResetPasswordController extends Controller
    {
        public function page(string $token): Factory|View|Application
        {
            return view('auth.reset-password', ['token' => $token]);
        }


        public function handle(ResetPasswordFormRequest $request): RedirectResponse
        {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => bcrypt($password)
                    ])->setRememberToken(str()->random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                flash()->info(__($status));

                return redirect()->route('login');
            }

            return back()->withErrors(['email' => __($status)]);

//            return $status === Password::PASSWORD_RESET
//                ? redirect()->route('login')->with('status', __($status))
//                : back()->withErrors(['email' => [__($status)]]);
        }

    }
