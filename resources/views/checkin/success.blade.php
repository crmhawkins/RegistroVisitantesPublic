<x-layout>
    <h1>{{ __('Registro completado con éxito.') }}</h1>
    
    <div style="text-align: center; padding: 40px 0;">
        <div style="font-size: 72px; margin-bottom: 20px;">✅</div>
        <p>{{ __('Los datos han sido guardados satisfactoriamente.') }}</p>
        <p style="color: var(--text-muted); margin-top: 15px; font-size: 0.9em;">
            {{ __('Redirigiendo a la página web en unos segundos...') }}
        </p>
    </div>

    @stack('scripts')
    <script>
        setTimeout(function() {
            window.location.href = 'https://www.apartamentosalgeciras.com';
        }, 3000);
    </script>
</x-layout>
