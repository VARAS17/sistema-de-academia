<x-layouts::auth :title="__('Log in')">
    <div class="fixed inset-0 flex flex-col lg:flex-row">
        
        <!-- SECCIÓN IZQUIERDA: IMAGEN -->
        <div class="relative hidden w-full lg:block lg:w-1/2">
            <img 
                src="{{ asset('pacifico.jpg') }}" 
                alt="Pacifico" 
                class="absolute inset-0 object-cover w-full h-full"
            >
        </div>

        <!-- SECCIÓN DERECHA: FORMULARIO -->
        <div class="flex items-center justify-center w-full bg-white lg:w-1/2 dark:bg-zinc-900">
            <div class="w-full max-w-sm px-8">
                
                <!-- Logo Manual (Laravel SVG) -->
                <div class="flex justify-center mb-8">
                    <svg class="w-12 h-12 text-red-600 fill-current" viewBox="0 0 62 65" symbol="laravel-logo"><path d="M5.9 5.1L5.9 5.1Q5.9 4.7 6 4.3 6.2 3.9 6.5 3.6 6.8 3.3 7.2 3.1 7.6 3 8 3L8 3 13.9 3Q14.3 3 14.7 3.1 15.1 3.3 15.4 3.6 15.7 3.9 15.9 4.3 16 4.7 16 5.1L16 5.1 16 11.2 26 17 26 5.1Q26 4.7 26.1 4.3 26.3 3.9 26.6 3.6 26.9 3.3 27.3 3.1 27.7 3 28.1 3L28.1 3 34 3Q34.4 3 34.8 3.1 35.2 3.3 35.5 3.6 35.8 3.9 36 4.3 36.1 4.7 36.1 5.1L36.1 5.1 36.1 11.2 46.1 17 46.1 11.1Q46.1 10.7 46.2 10.3 46.4 9.9 46.7 9.6 47 9.3 47.4 9.1 47.8 9 48.2 9L48.2 9 54.1 9Q54.5 9 54.9 9.1 55.3 9.3 55.6 9.6 55.9 9.9 56.1 10.3 56.2 10.7 56.2 11.1L56.2 11.1 56.2 53.9Q56.2 54.3 56.1 54.7 55.9 55.1 55.6 55.4 55.3 55.7 54.9 55.9 54.5 56 54.1 56L54.1 56 48.2 56Q47.8 56 47.4 55.9 47 55.7 46.7 55.4 46.4 55.1 46.2 54.7 46.1 54.3 46.1 53.9L46.1 53.9 46.1 48 36.1 42.2 36.1 53.9Q36.1 54.3 36 54.7 35.8 55.1 35.5 55.4 35.2 55.7 34.8 55.9 34.4 56 34 56L34 56 28.1 56Q27.7 56 27.3 55.9 26.9 55.7 26.6 55.4 26.3 55.1 26.1 54.7 26 54.3 26 53.9L26 53.9 26 48 16 42.2 16 59.9Q16 60.3 15.9 60.7 15.7 61.1 15.4 61.4 15.1 61.7 14.7 61.9 14.3 62 13.9 62L13.9 62 8 62Q7.6 62 7.2 61.9 6.8 61.7 6.5 61.4 6.2 61.1 6 60.7 5.9 60.3 5.9 59.9L5.9 59.9 5.9 5.1M16 11.2L6.1 5.5Q6.1 5.5 6.1 5.5 6.1 5.5 6.1 5.5L6.1 5.5 6.1 59.9Q6.1 59.9 6.1 59.9 6.1 59.9 6.1 59.9L6.1 59.9 15.9 59.9Q15.9 59.9 15.9 59.9 15.9 59.9 15.9 59.9L15.9 59.9 15.9 11.2Q15.9 11.2 15.9 11.2 15.9 11.2 16 11.2M26.1 11.2L26.1 17.1 36.1 22.9 36.1 11.2 26.2 5.5Q26.2 5.5 26.2 5.5 26.2 5.5 26.2 5.5L26.2 5.5 26.2 5.1Q26.2 5.1 26.2 5.1 26.2 5.1 26.2 5.1L26.2 5.1 28.1 5.1Q28.1 5.1 28.1 5.1 28.1 5.1 28.1 5.1L28.1 5.1 34 5.1Q34 5.1 34 5.1 34 5.1 34 5.1L34 5.1 34 5.5Q34 5.5 34 5.5 34 5.5 34 5.5L34 5.5 34.1 11.2Q34.1 11.2 34.1 11.2 34.1 11.2 34.1 11.2M46.1 17.1L46.1 48 56 53.7Q56 53.7 56 53.7 56 53.7 56 53.7L56 53.7 56 11.1Q56 11.1 56 11.1 56 11.1 56 11.1L56 11.1 54.1 11.1Q54.1 11.1 54.1 11.1 54.1 11.1 54.1 11.1L54.1 11.1 48.2 11.1Q48.2 11.1 48.2 11.1 48.2 11.1 48.2 11.1L48.2 11.1 48.1 17.1Q48.1 17.1 48.1 17.1 48.1 17.1 46.1 17.1M26.1 22.9L26.1 53.9Q26.1 53.9 26.1 53.9 26.1 53.9 26.1 53.9L26.1 53.9 28.1 53.9Q28.1 53.9 28.1 53.9 28.1 53.9 28.1 53.9L28.1 53.9 34 53.9Q34 53.9 34 53.9 34 53.9 34 53.9L34 53.9 34 28.7 26.1 24.1Q26.1 24.1 26.1 24.1 26.1 24.1 26.1 22.9M46.1 22.9L36.1 17.1 36.1 42.2 46 48Q46 48 46 48 46 48 46.1 48M16 17.1L26.1 22.9 26.1 17.1 16 11.2"></path></svg>
                </div>

                <div class="mb-6 text-center lg:text-left">
                    <flux:heading size="xl" level="1">{{ __('Log in') }}</flux:heading>
                    <flux:subheading>{{ __('Enter your credentials to access') }}</flux:subheading>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 text-sm font-medium text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
                    @csrf

                    <!-- Email Address -->
                    <flux:input
                        name="email"
                        :label="__('Email address')"
                        :value="old('email')"
                        type="email"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="email@example.com"
                    />

                    <!-- Password -->
                    <flux:input
                        name="password"
                        :label="__('Password')"
                        type="password"
                        required
                        autocomplete="current-password"
                        :placeholder="__('Password')"
                        viewable
                    />

                    <!-- Remember Me -->
                    <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

                    <!-- Botón de Login -->
                    <div class="pt-2">
                        <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                            {{ __('Log in') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts::auth>