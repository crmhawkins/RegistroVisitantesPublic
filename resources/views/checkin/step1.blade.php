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
        <h2 id="loader-text" style="color: var(--text-muted);">⏳ {{ __('Procesando...') }}</h2>
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

        function resizeImage(file, maxDim, callback) {
            if (!file || !file.type.match(/image.*/)) {
                return callback(file, false);
            }
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = new Image();
                img.onload = function() {
                    var width = img.width;
                    var height = img.height;
                    
                    if (width <= maxDim && height <= maxDim) {
                        return callback(file, false); // No need to resize
                    }
                    
                    if (width > height) {
                        if (width > maxDim) {
                            height = Math.round((height * maxDim) / width);
                            width = maxDim;
                        }
                    } else {
                        if (height > maxDim) {
                            width = Math.round((width * maxDim) / height);
                            height = maxDim;
                        }
                    }
                    
                    var canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    var ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    
                    if (canvas.toBlob) {
                        canvas.toBlob(function(blob) {
                            if (blob) {
                                callback(blob, true);
                            } else {
                                callback(file, false);
                            }
                        }, 'image/jpeg', 0.85);
                    } else {
                        callback(file, false);
                    }
                };
                img.onerror = function() { callback(file, false); };
                img.src = e.target.result;
            };
            reader.onerror = function() { callback(file, false); };
            reader.readAsDataURL(file);
        }

        function restoreUI() {
            document.getElementById('upload-form').style.display = 'block';
            document.getElementById('loader').style.display = 'none';
        }

        submitBtn.addEventListener('click', function() {
            if (frontInput.files.length === 0) return;

            document.getElementById('upload-form').style.display = 'none';
            document.getElementById('loader').style.display = 'block';
            var loaderText = document.getElementById('loader-text');
            loaderText.innerText = '⏳ {{ __("Reescalando imagen...") }}';

            var formData = new FormData();

            var sendData = function() {
                loaderText.innerText = '⏳ {{ __("Subiendo y procesando...") }}';
                
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
                            restoreUI();
                            return;
                        }
                        if (result.success || result.data) {
                            sessionStorage.setItem('ai_extracted_data', JSON.stringify(result.data || {}));
                            window.location.href = '{{ route("checkin.step2") }}';
                        } else {
                            restoreUI();
                            alert('{{ __("Error procesando la imagen.") }}');
                        }
                    } else if (xhr.status === 419) {
                        restoreUI();
                        alert('{{ __("Tu sesión ha caducado por inactividad. La página se recargará automáticamente para que puedas continuar de forma segura.") }}');
                        window.location.reload();
                    } else if (xhr.status === 422) {
                        restoreUI();
                        try {
                            var err = JSON.parse(xhr.responseText);
                            var msg = err.message || '{{ __("Verifique el formato y tamaño de las imágenes.") }}';
                            alert('{{ __("Error de validación:") }} ' + msg);
                        } catch(e) {
                            alert('{{ __("Error de validación en la imagen.") }}');
                        }
                    } else {
                        restoreUI();
                        alert('{{ __("Error de red o servidor. (Código ") }}' + xhr.status + ')');
                    }
                };

                xhr.onerror = function() {
                    restoreUI();
                    alert('{{ __("Error de conexión. Compruebe si tiene internet y vuelva a intentarlo.") }}');
                };

                xhr.send(formData);
            };

            // Process front image
            resizeImage(frontInput.files[0], 1920, function(frontBlob, isResized) {
                var frontName = frontInput.files[0].name;
                if (isResized && frontBlob.type === 'image/jpeg' && !frontName.toLowerCase().match(/\.(jpg|jpeg)$/)) {
                    frontName = frontName.replace(/\.[^/.]+$/, "") + ".jpg";
                }
                formData.append('dni_front', frontBlob, frontName);
                
                // Process back image if exists
                if (backInput.files.length > 0) {
                    resizeImage(backInput.files[0], 1920, function(backBlob, isBackResized) {
                        var backName = backInput.files[0].name;
                        if (isBackResized && backBlob.type === 'image/jpeg' && !backName.toLowerCase().match(/\.(jpg|jpeg)$/)) {
                            backName = backName.replace(/\.[^/.]+$/, "") + ".jpg";
                        }
                        formData.append('dni_back', backBlob, backName);
                        sendData();
                    });
                } else {
                    sendData();
                }
            });
        });
    </script>
</x-layout>
