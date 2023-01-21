<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\SignInFormRequest;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\View\View;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\View\Factory;

    use function auth;
    use function back;
    use function redirect;
    use function route;
    use function view;

    class SignInController extends Controller
    {
        public function page(): Factory|View|Application|RedirectResponse
        {
            return view('auth.login');
        }

        public function handle(SignInFormRequest $request): RedirectResponse
        {
            if (!auth()->attempt($request->validated())) {
                return back()->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ])->onlyInput('email');
            }


            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        public function logOut(): RedirectResponse
        {
            auth()->logout();

            request()->session()->invalidate();

            request()->session()->regenerateToken();

            return redirect()->route('home');
        }

    }
