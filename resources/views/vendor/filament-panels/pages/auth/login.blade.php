

{{-- Login Section --}}
        <div class="w-full lg:w-[420px] bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl p-8 sm:p-10">

            {{-- Mobile Logo --}}
            

            {{-- Register Link --}}
            @if (filament()->hasRegistration())
                <div class="mb-6 text-center text-sm text-gray-500">
                    {{ __('filament-panels::pages/auth/login.actions.register.before') }}
                    {{ $this->registerAction }}
                </div>
            @endif

            {{-- Hooks --}}
            {{ \Filament\Support\Facades\FilamentView::renderHook(
                \Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                scopes: $this->getRenderHookScopes()
            ) }}

            {{-- Form --}}
            
            <x-filament-panels::form wire:submit="authenticate" class="space-y-6">

                <div class="space-y-4">
                    {{ $this->form }} 
                </div>

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="true"
                />

            </x-filament-panels::form>

            {{ \Filament\Support\Facades\FilamentView::renderHook(
                \Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                scopes: $this->getRenderHookScopes()
            ) }}

            {{-- Footer Note --}}
            <p class="text-xs text-center text-gray-400 mt-6">
                © {{ date('Y') }} BKWART STUDIO. ALL RIGHTS RESERVED.
            </p>

        </>
