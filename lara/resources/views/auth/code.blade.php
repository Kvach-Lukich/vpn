<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>
{{ $wrongcode ?? '' }}
        <!-- Validation Errors -->

        <form method="POST" action="code">
            @csrf

            <!-- Token -->
            <div>
                <x-label for="token" :value="__('Mail code')" />

                <x-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code')" required autofocus />
            </div>
            
            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('resendcode') }}">
                    {{ __('Resend code?') }}
                </a>

                <x-button class="ml-4">
                    {{ __('Check Code') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
