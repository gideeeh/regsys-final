<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <!-- <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div> -->

        <!-- First Name -->
        <div>
            <x-input-label for="first_name" :value="__('First Name')" />
            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="first_name" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="mt-4">
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autofocus autocomplete="last_name" />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4" x-data="{ showPassword: false }">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-10"
                              x-bind:type="showPassword ? 'text' : 'password'"
                              name="password"
                              required autocomplete="new-password" />
                <div class="toggle-password">
                    <button type="button" tabindex="-1"
                        x-on:mousedown="showPassword = true" 
                        x-on:mouseup="showPassword = false" 
                        x-on:mouseleave="showPassword = false" 
                        x-on:touchstart="showPassword = true" 
                        x-on:touchend="showPassword = false">
                        <x-heroicon-o-eye x-show="!showPassword" class="w-6"/>
                        <x-go-eye-closed-16 x-show="showPassword" class="w-6"/>
                    </button>
                </div>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4" x-data="{ showConfirmPassword: false }">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <div class="relative">
                <x-text-input id="password_confirmation" class="block mt-1 w-full pr-10"
                              x-bind:type="showConfirmPassword ? 'text' : 'password'"
                              name="password_confirmation" required autocomplete="new-password" />
                <div class="toggle-password">
                    <button type="button" tabindex="-1"
                        x-on:mousedown="showConfirmPassword = true" 
                        x-on:mouseup="showConfirmPassword = false" 
                        x-on:mouseleave="showConfirmPassword = false" 
                        x-on:touchstart="showConfirmPassword = true" 
                        x-on:touchend="showConfirmPassword = false">
                        <x-heroicon-o-eye x-show="!showConfirmPassword" class="w-6"/>
                        <x-go-eye-closed-16 x-show="showConfirmPassword" class="w-6"/>
                    </button>
                </div>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4" >
            <a 
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                href="{{ route('login') }}"
                tabindex="-1">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
