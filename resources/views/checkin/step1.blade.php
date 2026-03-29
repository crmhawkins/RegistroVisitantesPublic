<x-layout>
    <h1>{{ __('Paso 1 - Toma de datos') }}</h1>

    <div id="upload-form">
        <div class="file-upload-wrapper" id="front-wrapper">
            <input type="file" id="dni_front" accept="image/*" capture="environment">
            <div class="file-upload-text" id="front-text">📷 {{ __('Foto DNI parte frontal') }}</div>
        </div>

        <div class="file-upload-wrapper" id="back-wrapper">
            <input type="file" id="dni_back" accept="image/*" capture="environment">
            <div class="file-upload-text" id="back-text">📷 {{ __('Foto DNI parte trasera') }}</div>
        </div>

        <button type="button" class="btn" id="submit-btn" disabled>{{ __('Siguiente') }}</button>
    </div>

    <div id="loader" style="display: none; text-align: center; padding: 40px 0;">
        <h2 style="color: var(--text-muted);">⏳ {{ __('Procesando...') }}</h2>
    </div>

    @stack('scripts')
    <script>
        const frontInput = document.getElementById('dni_front');
        const backInput = document.getElementById('dni_back');
        const frontText = document.getElementById('front-text');
        const backText = document.getElementById('back-text');
        const submitBtn = document.getElementById('submit-btn');

        function updateUI() {
            if (frontInput.files.length > 0) {
                frontText.innerHTML = "✅ " + frontInput.files[0].name;
                frontText.style.color = "green";
                submitBtn.disabled = false; // Solo front es estrictamente necesario para intentar
            }
            if (backInput.files.length > 0) {
                backText.innerHTML = "✅ " + backInput.files[0].name;
                backText.style.color = "green";
            }
        }

        frontInput.addEventListener('change', updateUI);
        backInput.addEventListener('change', updateUI);

        submitBtn.addEventListener('click', async () => {
            if (frontInput.files.length === 0) return;

            document.getElementById('upload-form').style.display = 'none';
            document.getElementById('loader').style.display = 'block';

            const formData = new FormData();
            formData.append('dni_front', frontInput.files[0]);
            if (backInput.files.length > 0) {
                formData.append('dni_back', backInput.files[0]);
            }

            try {
                const response = await fetch('{{ route("checkin.process") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();
                
                // Si la IA procesa bien o mal, siempre guardamos la metadata en SessionStorage para poblar
                // el formulario 2. El backend ya guardó las imágenes en sesión.
                if (result.data) {
                    sessionStorage.setItem('ai_extracted_data', JSON.stringify(result.data));
                } else {
                    sessionStorage.setItem('ai_extracted_data', JSON.stringify({}));
                }

                window.location.href = '{{ route("checkin.step2") }}';
            } catch (error) {
                console.error(error);
                // Si hay fallo de red, pasamos al paso 2 igual con datos vacíos
                sessionStorage.setItem('ai_extracted_data', JSON.stringify({}));
                window.location.href = '{{ route("checkin.step2") }}';
            }
        });
    </script>
</x-layout>
