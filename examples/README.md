# Examples

These files are **not loaded by the package** — copy what you need into your app.

## When a form is submitted

The renderer fires the Livewire event `form-submitted` and calls `FormRepositoryContract::saveSubmission()`. You have three clean integration points:

| # | Approach | File | Best for |
|---|---|---|---|
| 1 | Repository decorator | `ApiWebhookRepository.php` | Server-side webhook / all-in-one |
| 2 | Laravel event listener | `FormSubmissionListener.php` | Queued jobs, multiple handlers |
| 3 | Controller processing | `ControllerHandlingExample.php` | Traditional controller / API routes |
| 4 | Client-side JS | `api-form-page.blade.php` | Third-party SaaS, analytics, n8n/Make |

---

### 1 — Repository decorator (`ApiWebhookRepository.php`)

Wraps your existing Eloquent repository.
Saves to DB first, then POSTs a JSON payload to any HTTP endpoint.

```php
// AppServiceProvider::register()
$this->app->bind(FormRepositoryContract::class, function () {
    return new ApiWebhookRepository(
        inner:      new LivewireFormBuilderRepository(),
        webhookUrl: config('services.webhook.url'),
        secret:     config('services.webhook.secret'),  // for HMAC sig
    );
});
```

The webhook payload shape:
```json
{
  "event": "form.submitted",
  "submission_id": 42,
  "form_id": 7,
  "submitted_at": "2026-03-23T10:00:00+00:00",
  "ip": "1.2.3.4",
  "data": { "name": "Ada", "email": "ada@example.com" }
}
```

---

### 2 — Laravel event listener (`FormSubmissionListener.php`)

Fire a Laravel event from your repository's `saveSubmission()`, then handle it asynchronously. Add `implements ShouldQueue` to the listener for background processing.

```php
// In your saveSubmission():
event(new \App\Events\FormSubmitted($submission));
```

---

### 3 — Controller processing (`ControllerHandlingExample.php`)

Shows how to process a form submission through a traditional Laravel controller. This can be done by either:
- Receiving data via an API route (triggered by the JS event).
- Explicitly calling controller logic from an event listener.

---

### 4 — Client-side JS (`api-form-page.blade.php`)

Listen for the browser-side `form-submitted` event and call any HTTP endpoint with `fetch()`. No PHP changes required.

```js
Livewire.on('form-submitted', ({ formId, data }) => {
    fetch('/api/crm/contacts', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ form_id: formId, fields: data }),
    });
});
```
