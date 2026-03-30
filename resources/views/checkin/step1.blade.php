<x-layout>
    <h1>{{ __('Paso 1 - Toma de datos') }}</h1>

    <div id="upload-form">
        <div class="file-upload-wrapper" id="front-wrapper">
            <input type="file" id="dni_front" accept="image/*">
            <div class="file-upload-text" id="front-text">📷 {{ __('Foto DNI parte frontal') }}</div>
        </div>

        <div class="file-upload-wrapper" id="back-wrapper">
            <input type="file" id="dni_back" accept="image/*">
            <div class="file-upload-text" id="back-text">📷 {{ __('Foto DNI parte trasera') }}</div>
        </div>

        <button type="button" class="btn" id="submit-btn" disabled>{{ __('Siguiente') }}</button>
    </div>

    <div id="loader" style="display: none; text-align: center; padding: 40px 0;">
        <h2 style="color: var(--text-muted);">⏳ {{ __('Procesando...') }}</h2>
    </div>

    @stack('scripts')
    <script>
        var frontInput = document.getElementById('dni_front');
        var backInput = document.getElementById('dni_back');
        var frontText = document.getElementById('front-text');
        var backText = document.getElementById('back-text');
        var submitBtn = document.getElementById('submit-btn');

        function updateUI() {
            if (frontInput.files.length > 0) {
                frontText.innerHTML = "✅ " + frontInput.files[0].name;
                frontText.style.color = "green";
                submitBtn.disabled = false;
            }
            if (backInput.files.length > 0) {
                backText.innerHTML = "✅ " + backInput.files[0].name;
                backText.style.color = "green";
            }
        }

        frontInput.addEventListener('change', updateUI);
        backInput.addEventListener('change', updateUI);

        submitBtn.addEventListener('click', function() {
            if (frontInput.files.length === 0) return;

            document.getElementById('upload-form').style.display = 'none';
            document.getElementById('loader').style.display = 'block';

            var formData = new FormData();
            formData.append('dni_front', frontInput.files[0]);
            if (backInput.files.length > 0) {
                formData.append('dni_back', backInput.files[0]);
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("checkin.process") }}', true);
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfMeta.content);
            }
            xhr.setRequestHeader('Accept', 'application/json');

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    var result;
                    try {
                        result = JSON.parse(xhr.responseText);
                    } catch (e) {
                        alert('{{ __("Error del servidor, por favor intente de nuevo.") }}');
                        document.getElementById('upload-form').style.display = 'block';
                        document.getElementById('loader').style.display = 'none';
                        return;
                    }
                    if (result.success || result.data) {
                        sessionStorage.setItem('ai_extracted_data', JSON.stringify(result.data || {}));
                        window.location.href = '{{ route("checkin.step2") }}';
                    } else {
                        document.getElementById('upload-form').style.display = 'block';
                        document.getElementById('loader').style.display = 'none';
                        alert('{{ __("Error procesando la imagen.") }}');
                    }
                } else if (xhr.status === 422) {
                    document.getElementById('upload-form').style.display = 'block';
                    document.getElementById('loader').style.display = 'none';
                    try {
                        var err = JSON.parse(xhr.responseText);
                        var msg = err.message || '{{ __("Verifique el formato y tamaño (máx 20MB) de las imágenes.") }}';
                        alert('{{ __("Error de validación:") }} ' + msg);
                    } catch(e) {
                        alert('{{ __("Error de validación en la imagen.") }}');
                    }
                } else {
                    document.getElementById('upload-form').style.display = 'block';
                    document.getElementById('loader').style.display = 'none';
                    alert('{{ __("Error de red o servidor. (Código ") }}' + xhr.status + ')');
                }
            };

            xhr.onerror = function() {
                document.getElementById('upload-form').style.display = 'block';
                document.getElementById('loader').style.display = 'none';
                alert('{{ __("Error de conexión. Compruebe si tiene internet y vuelva a intentarlo.") }}');
            };

            xhr.send(formData);
        });
    </script>
</x-layout>
