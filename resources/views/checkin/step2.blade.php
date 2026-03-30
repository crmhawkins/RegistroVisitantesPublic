<x-layout>
    <h1>{{ __('Paso 2 - Confirmación de datos') }}</h1>
    <p style="color: var(--text-muted); margin-bottom: 24px;">{{ __('Por favor, revisa y completa los datos.') }}</p>

    @if ($errors->any())
        <div style="background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 24px;">
            <ul style="margin:0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                                var field = document.querySelector('[name="guests[0][' + key + ']"]');
                                // Set if exists and value is empty
                                if (field && !field.value) {
                                    field.value = data[key];
                                }
                            }
                        }
                    } catch (e) { console.error('Error parsing AI data', e); }
                }
            });
        </script>

        <div id="guests-container">
            <div class="guest-block" id="guest-block-0" style="padding-bottom: 20px;">
                <h3 style="margin-bottom: 16px; color: var(--primary);">{{ __('Huésped 1 (Titular)') }}</h3>
                
                <div class="form-group" id="relationship-group-0" style="display: none;">
                    <label>{{ __('Parentesco') }} *</label>
                    <select name="guests[0][relationship]" class="relationship-input">
                        <option value="">-- {{ __('Seleccione parentesco') }} --</option>
                        <option value="Abuelo/a" {{ old('guests.0.relationship') == 'Abuelo/a' ? 'selected' : '' }}>{{ __('Abuelo/a') }}</option>
                        <option value="Bisabuelo/a" {{ old('guests.0.relationship') == 'Bisabuelo/a' ? 'selected' : '' }}>{{ __('Bisabuelo/a') }}</option>
                        <option value="Bisnieto/a" {{ old('guests.0.relationship') == 'Bisnieto/a' ? 'selected' : '' }}>{{ __('Bisnieto/a') }}</option>
                        <option value="Cuñado/a" {{ old('guests.0.relationship') == 'Cuñado/a' ? 'selected' : '' }}>{{ __('Cuñado/a') }}</option>
                        <option value="Cónyuge" {{ old('guests.0.relationship') == 'Cónyuge' ? 'selected' : '' }}>{{ __('Cónyuge') }}</option>
                        <option value="Hermano/a" {{ old('guests.0.relationship') == 'Hermano/a' ? 'selected' : '' }}>{{ __('Hermano/a') }}</option>
                        <option value="Hijo/a" {{ old('guests.0.relationship') == 'Hijo/a' ? 'selected' : '' }}>{{ __('Hijo/a') }}</option>
                        <option value="Nieto/a" {{ old('guests.0.relationship') == 'Nieto/a' ? 'selected' : '' }}>{{ __('Nieto/a') }}</option>
                        <option value="Padre o Madre" {{ old('guests.0.relationship') == 'Padre o Madre' ? 'selected' : '' }}>{{ __('Padre o Madre') }}</option>
                        <option value="Sobrino/a" {{ old('guests.0.relationship') == 'Sobrino/a' ? 'selected' : '' }}>{{ __('Sobrino/a') }}</option>
                        <option value="Suegro/a" {{ old('guests.0.relationship') == 'Suegro/a' ? 'selected' : '' }}>{{ __('Suegro/a') }}</option>
                        <option value="Tío/a" {{ old('guests.0.relationship') == 'Tío/a' ? 'selected' : '' }}>{{ __('Tío/a') }}</option>
                        <option value="Tutor" {{ old('guests.0.relationship') == 'Tutor' ? 'selected' : '' }}>{{ __('Tutor') }}</option>
                        <option value="Yerno o Nuera" {{ old('guests.0.relationship') == 'Yerno o Nuera' ? 'selected' : '' }}>{{ __('Yerno o Nuera') }}</option>
                        <option value="Otro" {{ old('guests.0.relationship') == 'Otro' ? 'selected' : '' }}>{{ __('Otro') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ __('Nombre') }} *</label>
                    <input type="text" name="guests[0][first_name]" required value="{{ old('guests.0.first_name') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Apellidos') }} *</label>
                    <input type="text" name="guests[0][last_name]" required value="{{ old('guests.0.last_name') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Sexo') }} *</label>
                    <select name="guests[0][gender]" required>
                        <option value="">--</option>
                        <option value="M" {{ old('guests.0.gender') == 'M' ? 'selected' : '' }}>Masculino / Male</option>
                        <option value="F" {{ old('guests.0.gender') == 'F' ? 'selected' : '' }}>Femenino / Female</option>
                        <option value="O" {{ old('guests.0.gender') == 'O' ? 'selected' : '' }}>Otro / Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ __('Fecha de nacimiento') }} *</label>
                    <input type="date" name="guests[0][birth_date]" required value="{{ old('guests.0.birth_date') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Nacionalidad') }} *</label>
                    <input type="text" name="guests[0][nationality]" required value="{{ old('guests.0.nationality') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Tipo de documento') }} *</label>
                    <select name="guests[0][document_type]" required>
                        <option value="DNI" {{ old('guests.0.document_type') == 'DNI' ? 'selected' : '' }}>DNI</option>
                        <option value="Passport" {{ old('guests.0.document_type') == 'Passport' ? 'selected' : '' }}>Passport</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ __('Número de documento') }} *</label>
                    <input type="text" name="guests[0][document_number]" required value="{{ old('guests.0.document_number') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Número de soporte') }}</label>
                    <input type="text" name="guests[0][document_support_number]" value="{{ old('guests.0.document_support_number') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Fecha de expedición') }}</label>
                    <input type="date" name="guests[0][exp_date]" value="{{ old('guests.0.exp_date') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Fecha de caducidad') }} *</label>
                    <input type="date" name="guests[0][expiry_date]" required value="{{ old('guests.0.expiry_date') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Dirección') }} *</label>
                    <input type="text" name="guests[0][address]" required value="{{ old('guests.0.address') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Código postal') }} *</label>
                    <input type="text" name="guests[0][postal_code]" required value="{{ old('guests.0.postal_code') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Ciudad') }} *</label>
                    <input type="text" name="guests[0][city]" required value="{{ old('guests.0.city') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('País') }} *</label>
                    <input type="text" name="guests[0][country]" required value="{{ old('guests.0.country') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Teléfono') }}</label>
                    <input type="text" name="guests[0][phone]" value="{{ old('guests.0.phone') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('Email') }}</label>
                    <input type="email" name="guests[0][email]" value="{{ old('guests.0.email') }}">
                </div>
                
                <hr style="border: 0; border-top: 1px solid var(--border); margin: 32px 0;">
            </div>
        </div>

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
            <input type="hidden" name="signature_data" id="signature_data" value="{{ old('signature_data') }}">
            
            <div style="margin-top: 24px; text-align: center;">
                <button type="button" id="add-guest-btn" class="btn btn-secondary" style="background-color: #e2e8f0; color: #1e293b; border: 1px dashed #94a3b8; font-weight: bold; width: auto; padding: 12px 24px; border-radius: 8px;">
                    + {{ __('Añadir más huéspedes') }}
                </button>
            </div>
        </div>

        <button type="submit" class="btn" id="submit-form">{{ __('Confirmar y guardar') }}</button>
    </form>

    @stack('scripts')
    <script>
        // --- Multi-Guest Logic Vanilla JS (ES5) ---
        var guestCount = 1;
        var addGuestBtn = document.getElementById('add-guest-btn');
        var guestsContainer = document.getElementById('guests-container');

        addGuestBtn.addEventListener('click', function() {
            var originalBlock = document.getElementById('guest-block-0');
            var clone = originalBlock.cloneNode(true);
            
            clone.id = 'guest-block-' + guestCount;
            
            // Update Title
            var h3 = clone.querySelector('h3');
            if (h3) {
                h3.innerText = '{{ __("Huésped") }} ' + (guestCount + 1);
            }

            // Show Relationship Field and Make it Required
            var relGroup = clone.querySelector('#relationship-group-0');
            if (relGroup) {
                relGroup.id = 'relationship-group-' + guestCount;
                relGroup.style.display = 'block';
                var relInput = relGroup.querySelector('.relationship-input');
                if (relInput) {
                    relInput.required = true;
                }
            }

            // Update Input Names and Values
            var inputs = clone.querySelectorAll('input, select');
            for (var i = 0; i < inputs.length; i++) {
                var input = inputs[i];
                if (input.name) {
                    input.name = input.name.replace('guests[0]', 'guests[' + guestCount + ']');
                }
                // Clear values except for selects or checkboxes
                if (input.tagName === 'INPUT') {
                    input.value = '';
                } else if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                }
            }

            // Remove potentially nested remove buttons if we accidentally cloned one
            var existingRemoveBtns = clone.querySelectorAll('.remove-guest-btn');
            for (var j = 0; j < existingRemoveBtns.length; j++) {
                existingRemoveBtns[j].parentNode.removeChild(existingRemoveBtns[j]);
            }

            // Add a Remove Button for this clone
            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-secondary remove-guest-btn';
            removeBtn.style.marginTop = '16px';
            removeBtn.style.backgroundColor = '#fee2e2';
            removeBtn.style.color = '#b91c1c';
            removeBtn.style.border = '1px solid #f87171';
            removeBtn.innerText = '{{ __("Eliminar huésped") }}';
            removeBtn.onclick = function() {
                guestsContainer.removeChild(clone);
            };
            clone.appendChild(removeBtn);

            guestsContainer.appendChild(clone);
            guestCount++;
            
            // Scroll to new guest smoothly
            if (clone.scrollIntoView) {
                clone.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        // --- Signature Canvas Logic Vanilla JS (ES5 Compatible) ---
        var canvas = document.getElementById('signature-pad');
        var submitForm = document.getElementById('checkin-form');
        var signatureData = document.getElementById('signature_data');
        var clearBtn = document.getElementById('clear-signature');
        var submitBtnEl = document.getElementById('submit-form');
        var ctx = canvas.getContext('2d');
        
        var isDrawing = false;
        var hasSignature = false;
        var isSubmitting = false; // Prevent double submit

        function resizeCanvas() {
            var dataUrl = hasSignature ? canvas.toDataURL() : null;
            
            var ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            ctx.scale(ratio, ratio);
            ctx.strokeStyle = '#000';
            ctx.lineWidth = 3;
            ctx.lineCap = 'round';
            
            // Si el móvil se gira y cambia tamaño, no borramos la firma
            if (dataUrl) {
                var img = new Image();
                img.onload = function() {
                    ctx.drawImage(img, 0, 0, canvas.offsetWidth, canvas.offsetHeight);
                };
                img.src = dataUrl;
            }
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        // Autorestaurar firma si la página recargó por error de validación
        if (signatureData.value) {
            var oldImg = new Image();
            oldImg.onload = function() {
                ctx.drawImage(oldImg, 0, 0, canvas.offsetWidth, canvas.offsetHeight);
                hasSignature = true;
            };
            oldImg.src = signatureData.value;
        }

        function getCoordinates(e) {
            var rect = canvas.getBoundingClientRect();
            if (e.touches && e.touches.length > 0) {
                return { x: e.touches[0].clientX - rect.left, y: e.touches[0].clientY - rect.top };
            }
            return { x: e.clientX - rect.left, y: e.clientY - rect.top };
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

        function stopDrawing() { isDrawing = false; }

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
            if (isSubmitting) {
                e.preventDefault();
                return;
            }
            
            if (!hasSignature) {
                e.preventDefault();
                alert('{{ __("La firma es obligatoria.") }}');
                return;
            }
            signatureData.value = canvas.toDataURL('image/png');
            isSubmitting = true;
            submitBtnEl.innerText = '{{ __("Procesando, por favor espere...") }}';
            submitBtnEl.style.opacity = '0.7';
            submitBtnEl.style.cursor = 'not-allowed';
        });

        // --- Restore Dynamic Validation Errors ---
        document.addEventListener("DOMContentLoaded", function() {
            var oldGuestsData = @json(old('guests'));
            if (oldGuestsData && typeof oldGuestsData === 'object') {
                var keys = Object.keys(oldGuestsData);
                // We already have index 0 rendered in HTML. Clone for the rest.
                for (var i = 1; i < keys.length; i++) {
                    var idx = keys[i];
                    addGuestBtn.click(); // Spawns a new clean block with the correctly incremented ID
                    
                    // Fill the newly spawned block with the old data
                    var newlySpawnedIndex = guestCount - 1; 
                    var oldGuest = oldGuestsData[idx];
                    
                    if (oldGuest) {
                        for (var fieldName in oldGuest) {
                            if (oldGuest.hasOwnProperty(fieldName)) {
                                var input = document.querySelector('[name="guests[' + newlySpawnedIndex + '][' + fieldName + ']"]');
                                if (input) {
                                    input.value = oldGuest[fieldName];
                                }
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-layout>
