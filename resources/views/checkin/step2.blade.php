<x-layout>
    <h1>{{ __('Paso 2 - Confirmación de datos') }}</h1>
    <p style="color: var(--text-muted); margin-bottom: 24px;">{{ __('Por favor, revisa y completa los datos.') }}</p>

    <form method="POST" action="{{ route('checkin.store') }}" id="checkin-form">
        @csrf

        <!-- AI Prefill Script -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var dataStr = sessionStorage.getItem('ai_extracted_data');
                if (dataStr) {
                    try {
                        var data = JSON.parse(dataStr);
                        for (var key in data) {
                            if (data.hasOwnProperty(key)) {
                                var field = document.getElementById(key);
                                // Set if exists and value is empty (don't overwrite user changes if reloaded)
                                if (field && !field.value) {
                                    field.value = data[key];
                                }
                            }
                        }
                    } catch (e) { console.error('Error parsing AI data', e); }
                }
            });
        </script>

        <div class="form-group">
            <label for="first_name">{{ __('Nombre') }} *</label>
            <input type="text" id="first_name" name="first_name" required value="{{ old('first_name') }}">
        </div>

        <div class="form-group">
            <label for="last_name">{{ __('Apellidos') }} *</label>
            <input type="text" id="last_name" name="last_name" required value="{{ old('last_name') }}">
        </div>

        <div class="form-group">
            <label for="gender">{{ __('Sexo') }} *</label>
            <select id="gender" name="gender" required>
                <option value="">--</option>
                <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculino / Male</option>
                <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Femenino / Female</option>
                <option value="O" {{ old('gender') == 'O' ? 'selected' : '' }}>Otro / Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="birth_date">{{ __('Fecha de nacimiento') }} *</label>
            <input type="date" id="birth_date" name="birth_date" required value="{{ old('birth_date') }}">
        </div>

        <div class="form-group">
            <label for="nationality">{{ __('Nacionalidad') }} *</label>
            <input type="text" id="nationality" name="nationality" required value="{{ old('nationality') }}">
        </div>

        <div class="form-group">
            <label for="document_type">{{ __('Tipo de documento') }} *</label>
            <select id="document_type" name="document_type" required>
                <option value="DNI" {{ old('document_type') == 'DNI' ? 'selected' : '' }}>DNI</option>
                <option value="Passport" {{ old('document_type') == 'Passport' ? 'selected' : '' }}>Passport</option>
            </select>
        </div>

        <div class="form-group">
            <label for="document_number">{{ __('Número de documento') }} *</label>
            <input type="text" id="document_number" name="document_number" required value="{{ old('document_number') }}">
        </div>

        <div class="form-group">
            <label for="document_support_number">{{ __('Número de soporte') }}</label>
            <input type="text" id="document_support_number" name="document_support_number" value="{{ old('document_support_number') }}">
        </div>

        <div class="form-group">
            <label for="exp_date">{{ __('Fecha de expedición') }}</label>
            <input type="date" id="exp_date" name="exp_date" value="{{ old('exp_date') }}">
        </div>

        <div class="form-group">
            <label for="expiry_date">{{ __('Fecha de caducidad') }} *</label>
            <input type="date" id="expiry_date" name="expiry_date" required value="{{ old('expiry_date') }}">
        </div>

        <div class="form-group">
            <label for="address">{{ __('Dirección') }} *</label>
            <input type="text" id="address" name="address" required value="{{ old('address') }}">
        </div>

        <div class="form-group">
            <label for="postal_code">{{ __('Código postal') }} *</label>
            <input type="text" id="postal_code" name="postal_code" required value="{{ old('postal_code') }}">
        </div>

        <div class="form-group">
            <label for="city">{{ __('Ciudad') }} *</label>
            <input type="text" id="city" name="city" required value="{{ old('city') }}">
        </div>

        <div class="form-group">
            <label for="country">{{ __('País') }} *</label>
            <input type="text" id="country" name="country" required value="{{ old('country') }}">
        </div>

        <div class="form-group">
            <label for="phone">{{ __('Teléfono') }}</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}">
        </div>

        <div class="form-group">
            <label for="email">{{ __('Email') }}</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}">
        </div>

        <hr style="border: 0; border-top: 1px solid var(--border); margin: 32px 0;">

        <!-- BOOKING BLOCKS -->
        <div class="form-group">
            <label for="check_in_date">{{ __('Fecha de entrada') }} *</label>
            <input type="date" id="check_in_date" name="check_in_date" required value="{{ old('check_in_date') }}">
        </div>

        <div class="form-group">
            <label for="check_out_date">{{ __('Fecha de salida') }} *</label>
            <input type="date" id="check_out_date" name="check_out_date" required value="{{ old('check_out_date') }}">
        </div>

        <div class="form-group">
            <label for="payment_method">{{ __('Medio de pago') }} *</label>
            <select id="payment_method" name="payment_method" required>
                <option value="Tarjeta" {{ old('payment_method') == 'Tarjeta' ? 'selected' : '' }}>{{ __('Tarjeta') }}</option>
                <option value="Efectivo" {{ old('payment_method') == 'Efectivo' ? 'selected' : '' }}>{{ __('Efectivo') }}</option>
                <option value="Transferencia" {{ old('payment_method') == 'Transferencia' ? 'selected' : '' }}>{{ __('Transferencia') }}</option>
            </select>
        </div>

        <hr style="border: 0; border-top: 1px solid var(--border); margin: 32px 0;">

        <!-- ACCEPTANCE AND SIGNATURE -->
        <div class="form-group" style="background:#f9fafb; padding:20px; border-radius:12px; border:1px solid var(--border);">
            <label style="display: flex; align-items: start; gap:12px; font-weight: normal; margin-bottom: 8px;">
                <input type="checkbox" name="terms_accepted" id="terms_accepted" required style="width: 24px; height: 24px; margin-top: 2px;">
                <span>{{ __('Al aceptar, usted acepta las condiciones de uso de nuestras instalaciones') }}</span>
            </label>
            <a href="#" style="color: var(--focus); margin-left: 36px; display:inline-block; margin-bottom: 20px;">{{ __('Leer condiciones') }}</a>

            <label>{{ __('Firma aquí:') }} *</label>
            <div style="border: 2px solid var(--border); border-radius: var(--border-radius); background: #fff; overflow:hidden;">
                <canvas id="signature-pad" style="width: 100%; height: 200px; touch-action: none; display: block;"></canvas>
            </div>
            <button type="button" id="clear-signature" class="btn btn-secondary" style="margin-top: 12px; font-size: 16px; padding: 12px;">{{ __('Limpiar firma') }}</button>
            <input type="hidden" name="signature_data" id="signature_data">
        </div>

        <button type="submit" class="btn" id="submit-form">{{ __('Confirmar y guardar') }}</button>
    </form>

    @stack('scripts')
    <script>
        // Signature Canvas Logic Vanilla JS (ES5 Compatible)
        var canvas = document.getElementById('signature-pad');
        var submitForm = document.getElementById('checkin-form');
        var signatureData = document.getElementById('signature_data');
        var clearBtn = document.getElementById('clear-signature');
        var ctx = canvas.getContext('2d');
        
        var isDrawing = false;
        var hasSignature = false;

        // Resize canvas to fix blur on high DPI screens and mapping
        function resizeCanvas() {
            var ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            ctx.scale(ratio, ratio);
            ctx.strokeStyle = '#000';
            ctx.lineWidth = 3;
            ctx.lineCap = 'round';
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        function getCoordinates(e) {
            var rect = canvas.getBoundingClientRect();
            // Para touch
            if (e.touches && e.touches.length > 0) {
                return {
                    x: e.touches[0].clientX - rect.left,
                    y: e.touches[0].clientY - rect.top
                };
            }
            // Para mouse
            return {
                x: e.clientX - rect.left,
                y: e.clientY - rect.top
            };
        }

        function startDrawing(e) {
            isDrawing = true;
            hasSignature = true;
            var pos = getCoordinates(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
            e.preventDefault();
        }

        function draw(e) {
            if (!isDrawing) return;
            var pos = getCoordinates(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            e.preventDefault();
        }

        function stopDrawing() {
            isDrawing = false;
        }

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        canvas.addEventListener('touchstart', startDrawing);
        canvas.addEventListener('touchmove', draw);
        canvas.addEventListener('touchend', stopDrawing);

        clearBtn.addEventListener('click', function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasSignature = false;
            signatureData.value = '';
        });

        submitForm.addEventListener('submit', function(e) {
            if (!hasSignature) {
                e.preventDefault();
                alert('{{ __("La firma es obligatoria.") }}');
                return;
            }
            signatureData.value = canvas.toDataURL('image/png');
        });
    </script>
</x-layout>
