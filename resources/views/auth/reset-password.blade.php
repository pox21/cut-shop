@extends('layouts.auth')

@section('title', 'Восстановление пароля')
@section('content')
    <x-forms.auth-forms title="Восстановление пароля" action="{{ route('password-reset.handle') }}" method="POST">

        <input type="hidden" name="token" value="{{ $token }}">

        <x-forms.text-input
            :isError="$errors->has('email')"
            name="email"
            type="email"
            placeholder="E-mail"
            value="{{ request('email') }}"

            required
        />
        @error('email')
        <x-forms.error>
            {{ $message }}
        </x-forms.error>
        @enderror

        <x-forms.text-input
            :isError="$errors->has('password')"
            name="password"
            type="password"
            placeholder="Пароль"
            required
        />

        @error('password')
        <x-forms.error>
            {{ $message }}
        </x-forms.error>
        @enderror

        <x-forms.text-input
            :isError="$errors->has('password_confirmation')"
            name="password_confirmation"
            type="password"
            placeholder="Повторите пароль"
            required
        />
        @error('password_confirmation')
        <x-forms.error>
            {{ $message }}
        </x-forms.error>
        @enderror

        <x-forms.primary-button>Обновить пароль</x-forms.primary-button>

        <x-slot:socialAuth></x-slot:socialAuth>
        <x-slot:buttons></x-slot:buttons>
    </x-forms.auth-forms>
@endsection
