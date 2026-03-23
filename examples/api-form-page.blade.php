{{--
EXAMPLE: api-form-page.blade.php  (client-side approach)

Use this when you want to call a REST API from the browser after a successful
form submission — for example to sync data to a SaaS, analytics, or a custom
backend endpoint — without writing any server-side PHP.

The renderer dispatches a JavaScript custom event "form-submitted" via
Livewire's dispatch() as soon as the submission is saved.  You can listen to
it from any vanilla JS or Alpine component on the same page.

────────────────────────────────────────────────────────────────────────────

HOW THE EVENT LOOKS
  Livewire.on('form-submitted', ({ formId, data }) => { … })

  formId  – the database ID of the form (null when using :schema= directly)
  data    – the validated key→value map of all submitted field values

────────────────────────────────────────────────────────────────────────────
--}}

<x-layouts.app>   {{-- replace with your own layout component --}}

    <div class="max-w-2xl mx-auto py-12 px-4">
        <flux:heading size="xl" class="mb-8">Contact</flux:heading>

        {{-- The renderer handles validation, conditional logic, and file
             uploads.  All you do here is pass the form-id (or a raw schema). --}}
        <livewire:livewire-form-builder::renderer
            :form-id="$form->id"
            success-message="Thank you! We will be in touch shortly."
        />
    </div>

    {{-- ── Client-side API handler ─────────────────────────────────────── --}}
    <script>
    document.addEventListener('livewire:init', () => {

        Livewire.on('form-submitted', ({ formId, data }) => {

            // ── Option A: POST to your own Laravel API route ────────────
            fetch('/api/crm/contacts', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    // Include CSRF token if the route uses web middleware:
                    // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    source:  'form-builder',
                    form_id: formId,
                    fields:  data,
                }),
            })
            .then(r => r.json())
            .then(json => console.log('CRM response:', json))
            .catch(err => console.error('CRM push failed:', err));

            // ── Option B: Third-party webhook (e.g. Make / n8n / Zapier) ─
            // fetch('https://hook.eu2.make.com/YOUR_HOOK_ID', {
            //     method: 'POST',
            //     headers: { 'Content-Type': 'application/json' },
            //     body: JSON.stringify({ form_id: formId, ...data }),
            // });

            // ── Option C: Push to analytics ──────────────────────────────
            // gtag('event', 'form_submit', { form_id: formId });
        });

    });
    </script>

</x-layouts.app>
