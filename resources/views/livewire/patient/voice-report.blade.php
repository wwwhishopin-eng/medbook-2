<div dir="rtl">
    {{-- Trigger button --}}
    <button class="btn-primary" wire:click="openModal"
            style="background:linear-gradient(135deg,#0E8F72,#059669);padding:8px 18px;font-size:13px;">
        <svg width="14" height="14" style="display:inline;margin-left:5px;vertical-align:-2px"
             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
            <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
            <line x1="12" y1="19" x2="12" y2="23"/>
            <line x1="8" y1="23" x2="16" y2="23"/>
        </svg>
        گزارش صوتی
    </button>

    @if($isOpen)
    <div class="modal-overlay open" style="z-index:250;">
        <div class="modal" style="max-width:600px;">

            {{-- Header --}}
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                <h3 style="font-size:18px;font-weight:700;color:#111A6B;margin:0;">
                    گزارش صوتی بیمار
                </h3>
                <button wire:click="closeModal"
                        style="background:none;border:none;cursor:pointer;font-size:20px;color:#9CA3AF;">✕</button>
            </div>

            {{-- Patient info --}}
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;background:#F9FAFB;padding:12px;border-radius:12px;">
                <div class="avatar" style="background:{{ $patient->avatar_color }}22;color:{{ $patient->avatar_color }};width:36px;height:36px;font-size:13px;">
                    {{ $patient->avatar_initial }}
                </div>
                <span style="font-size:14px;font-weight:600;color:#111827;">{{ $patient->full_name }}</span>
            </div>

            {{-- Step 1: Record or type --}}
            @if(!$structuredReport)
            <div>
                <label class="field-label">گزارش پزشکی (صوتی یا متنی)</label>
                <p style="font-size:12px;color:#6B7280;margin:0 0 12px;">
                    به صورت طبیعی صحبت کنید. مثال: «بیمار با درد دندان مراجعه کرد. عصب کشی انجام شد. آموکسی سیلین تجویز شد. دو هفته دیگر مراجعه کند.»
                </p>

                {{-- Voice recording buttons --}}
                <div style="display:flex;gap:8px;margin-bottom:12px;">
                    @if(!$isRecording)
                    <button wire:click="startRecording" class="btn-primary"
                            style="background:linear-gradient(135deg,#DC2626,#B91C1C);font-size:13px;">
                        <svg width="14" height="14" style="display:inline;margin-left:4px;vertical-align:-2px"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                            <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                        </svg>
                        شروع ضبط
                    </button>
                    @else
                    <button wire:click="stopRecording" class="btn-primary"
                            style="background:linear-gradient(135deg,#15803D,#0E8F72);font-size:13px;">
                        توقف ضبط
                    </button>
                    @endif
                </div>

                {{-- Transcript textarea --}}
                <textarea wire:model="transcript" class="form-input" rows="5"
                          placeholder="متن گزارش پزشکی اینجا نمایش داده می‌شود یا مستقیم تایپ کنید..."></textarea>
                @error('transcript') <p class="field-error">{{ $message }}</p> @enderror

                <button wire:click="processTranscript" class="btn-primary"
                        style="margin-top:14px;width:100%;justify-content:center;">
                    پردازش و ساخت گزارش
                </button>
            </div>
            @endif

            {{-- Step 2: Structured report preview --}}
            @if($structuredReport)
            <div>
                <h4 style="font-size:15px;font-weight:700;color:#111A6B;margin:0 0 16px;">
                    گزارش ساختاریافته
                </h4>

                <div style="display:grid;gap:12px;">
                    @if($structuredReport['chief_complaint'])
                    <div style="background:#FEF9C3;border-radius:10px;padding:12px;">
                        <p style="font-size:11px;font-weight:600;color:#854D0E;margin:0 0 4px;">شکایت اصلی</p>
                        <p style="font-size:13px;color:#111827;margin:0;">{{ $structuredReport['chief_complaint'] }}</p>
                    </div>
                    @endif

                    @if($structuredReport['diagnosis'])
                    <div style="background:#EEF4FF;border-radius:10px;padding:12px;">
                        <p style="font-size:11px;font-weight:600;color:#1D4ED8;margin:0 0 4px;">تشخیص</p>
                        <p style="font-size:13px;color:#111827;margin:0;">{{ $structuredReport['diagnosis'] }}</p>
                    </div>
                    @endif

                    @if($structuredReport['treatment'])
                    <div style="background:#F0FDF4;border-radius:10px;padding:12px;">
                        <p style="font-size:11px;font-weight:600;color:#15803D;margin:0 0 4px;">درمان</p>
                        <p style="font-size:13px;color:#111827;margin:0;">{{ $structuredReport['treatment'] }}</p>
                    </div>
                    @endif

                    @if($structuredReport['prescriptions'])
                    <div style="background:#EDFAF6;border-radius:10px;padding:12px;">
                        <p style="font-size:11px;font-weight:600;color:#0E8F72;margin:0 0 4px;">نسخه / داروها</p>
                        <p style="font-size:13px;color:#065047;margin:0;">{{ $structuredReport['prescriptions'] }}</p>
                    </div>
                    @endif

                    @if($structuredReport['follow_up'])
                    <div style="background:#FEE2E2;border-radius:10px;padding:12px;">
                        <p style="font-size:11px;font-weight:600;color:#991B1B;margin:0 0 4px;">پیگیری</p>
                        <p style="font-size:13px;color:#111827;margin:0;">{{ $structuredReport['follow_up'] }}</p>
                    </div>
                    @endif
                </div>

                {{-- Follow-up suggestion --}}
                @if($showFollowUp && $followUpSuggestion)
                <div style="margin-top:16px;background:#F0FDF4;border:2px solid #0E8F72;border-radius:12px;padding:16px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0E8F72" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                        </svg>
                        <span style="font-size:13px;color:#065F46;">
                            نوبت پیگیری در {{ $followUpSuggestion['label'] }} ({{ $followUpSuggestion['date_jalali'] }}) ثبت خواهد شد.
                        </span>
                    </div>
                </div>
                @endif

                <div style="display:flex;gap:10px;margin-top:20px;justify-content:flex-end;">
                    <button class="btn-ghost" wire:click="$set('structuredReport', null)">ویرایش متن</button>
                    <button class="btn-primary" wire:click="saveReport">ذخیره گزارش</button>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- Web Speech API for voice recording --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('start-voice-recording', () => {
                if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                    alert('مرورگر شما از تشخیص صدا پشتیبانی نمی‌کند. لطفاً متن را مستقیم وارد کنید.');
                    return;
                }

                const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                const recognition = new SpeechRecognition();
                recognition.lang = 'fa-IR';
                recognition.continuous = true;
                recognition.interimResults = true;

                let finalTranscript = '';
                recognition.onresult = (event) => {
                    let interim = '';
                    for (let i = event.resultIndex; i < event.results.length; i++) {
                        if (event.results[i].isFinal) {
                            finalTranscript += event.results[i][0].transcript + ' ';
                        } else {
                            interim += event.results[i][0].transcript;
                        }
                    }
                    const textarea = document.querySelector('[wire\\:model="transcript"]');
                    if (textarea) {
                        textarea.value = finalTranscript + interim;
                        textarea.dispatchEvent(new Event('input'));
                    }
                };

                recognition.onerror = (event) => {
                    console.error('Speech recognition error:', event.error);
                };

                window.__voiceRecognition = recognition;
                recognition.start();
            });

            Livewire.on('stop-voice-recording', () => {
                if (window.__voiceRecognition) {
                    window.__voiceRecognition.stop();
                    window.__voiceRecognition = null;
                }
            });
        });
    </script>
</div>
