<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Validation\Rules\Password;
    use Worksome\RequestFactories\Concerns\HasFactory;

    class SignUpFormRequest extends FormRequest
    {
        use HasFactory;

        public function authorize(): bool
        {
            return auth()->guest();
        }

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array<string, mixed>
         */
        public function rules(): array
        {
            return [
                'name' => ['required', 'string', 'min:2'],
                'email' => ['required', 'email:dns', 'unique:users'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ];
        }

        protected function prepareForValidation()
        {
            $this->merge([
                'email' => str(request('email'))
                    ->squish()
                    ->lower()
                    ->value()
            ]);
        }
    }
