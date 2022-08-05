<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
{{ $noinvite ?? '' }}

        <form method="POST" action="{{ route('invite') }}">
            @csrf

            <!-- Token -->
            <div>
                <x-label for="token" :value="__('Invite code')" />

                <x-input id="invite" class="block mt-1 w-full" type="text" name="invite" :value="old('invite')" required autofocus />
            </div>
            
            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ml-4">
                    {{ __('Check Invite') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>