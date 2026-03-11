# Laravel Form Architect

A powerful, drag-and-drop form builder for **Laravel 12** and **Livewire 5** — a drop-in replacement for form.io.

The package ships **no Models and no Migrations**. You own your data layer. The package communicates with your app through a clean `FormRepositoryContract` interface.

---

## Features

| Feature | |
|---|---|
| Drag & Drop builder canvas | ✅ |
| **15 field types** | ✅ |
| Multi-column layout (full, 1/2, 1/3, 2/3, 1/4, 3/4) | ✅ |
| Repeater groups with nested columns | ✅ |
| Conditional logic (show/hide per AND/OR rules) | ✅ |
| Real-time per-field validation | ✅ |
| File uploads (single & multiple) | ✅ |
| JSON schema import / export | ✅ |
| CSV submission export | ✅ |
| Repository pattern — bring your own Model | ✅ |
| Artisan scaffolding commands | ✅ |
| Fully publishable views | ✅ |

### Field types

| Group | Types |
|---|---|
| **Inputs** | `text`, `textarea`, `number`, `select`, `checkbox`, `radio`, `toggle`, `datetime`, `file`, `repeater`, `hidden` |
| **Layout** | `heading`, `hint`, `html`, `divider` |

---

## Requirements

- PHP `^8.2`
- Laravel `^12.0`
- Livewire `^5.0`

---

## Installation

```bash
composer require tivents/livewire-form-builder
```

### 1. Publish config

```bash
php artisan vendor:publish --tag=livewire-form-builder-config
```

### 2. Publish and run stubs (migration + repository)

```bash
php artisan livewire-form-builder:publish-stubs
php artisan migrate
```

This places two files in your project:

| File | Purpose |
|---|---|
| `database/migrations/xxxx_create_livewire_form_builder_tables.php` | Creates `forms` + `form_submissions` tables |
| `app/Repositories/LivewireFormBuilderRepository.php` | Eloquent implementation of `FormRepositoryContract` |

You are free to rename tables, add columns, or swap out Eloquent for anything else — as long as your repository implements the contract.

### 3. Bind the repository

In `app/Providers/AppServiceProvider.php`:

```php
use Tivents\LivewireFormBuilder\Contracts\FormRepositoryContract;
use App\Repositories\LivewireFormBuilderRepository;

public function register(): void
{
    $this->app->bind(FormRepositoryContract::class, LivewireFormBuilderRepository::class);
}
```

Or set it in `config/livewire-form-builder.php`:

```php
'repository' => \App\Repositories\LivewireFormBuilderRepository::class,
```

---

## Usage

### Admin builder UI

Navigate to `/livewire-form-builder` — requires the middleware configured in `config/livewire-form-builder.php` (`auth` by default).

### Embed the builder in your own view

```blade
{{-- Create new form --}}
<livewire:livewire-form-builder::builder />

{{-- Edit existing form --}}
<livewire:livewire-form-builder::builder :form-id="$form->id" />
```

### Embed the renderer (public-facing)

```blade
{{-- By form ID --}}
<livewire:livewire-form-builder::renderer :form-id="$form->id" />

{{-- Inline schema (no DB needed) --}}
<livewire:livewire-form-builder::renderer :schema="$schemaArray" />

{{-- Custom success message --}}
<livewire:livewire-form-builder::renderer
    :form-id="$form->id"
    success-message="Vielen Dank für Ihre Anfrage!" />
```

### Listen to JS events

```javascript
document.addEventListener('livewire:init', () => {
    Livewire.on('form-submitted', ({ formId, data }) => {
        console.log('Submitted!', data);
    });
    Livewire.on('form-saved', ({ formId }) => {
        console.log('Builder saved form', formId);
    });
});
```

---

## Configuration (`config/livewire-form-builder.php`)

```php
return [
    // Your repository implementation
    'repository' => \App\Repositories\LivewireFormBuilderRepository::class,

    // URL prefix for the built-in admin routes
    'route_prefix'   => 'livewire-form-builder',
    'middleware'     => ['web', 'auth'],
    'builder_routes' => true,   // false to disable built-in CRUD routes

    // Pagination
    'per_page' => 25,

    // File upload
    'disk'             => 'public',
    'upload_directory' => 'livewire-form-builder/uploads',
    'max_file_size'    => 10240,   // KB

    // Register custom field types
    'field_types' => [
        // 'signature' => \App\FormFields\SignatureField::class,
    ],
];
```

---

## Adding a Custom Field Type

Use the scaffold command:

```bash
php artisan livewire-form-builder:make-field StarRating
```

This generates:

- `app/FormFields/StarRatingField.php` — implement your logic
- `resources/views/vendor/livewire-form-builder/fields/star_rating.blade.php` — renderer view
- `resources/views/vendor/livewire-form-builder/settings/star_rating.blade.php` — builder settings panel

Then register it:

```php
// config/livewire-form-builder.php
'field_types' => [
    'star_rating' => \App\FormFields\StarRatingField::class,
],
```

---

## JSON Schema Format

```json
{
  "name": "Kontaktformular",
  "schema": [
    { "type": "heading", "key": "h1", "text": "Kontakt", "level": "h2", "width": "full" },
    { "type": "text",    "key": "name_abc", "label": "Name",  "required": true, "width": "1/2" },
    { "type": "text",    "key": "email_xyz","label": "E-Mail","required": true, "width": "1/2", "input_type": "email" },
    {
      "type": "select", "key": "topic_def", "label": "Thema", "width": "full",
      "options": [{ "label": "Vertrieb", "value": "sales" }, { "label": "Support", "value": "support" }]
    },
    {
      "type": "textarea", "key": "msg_ghi", "label": "Nachricht", "required": true, "rows": 5,
      "conditions": {
        "action": "show", "logic": "and",
        "rules": [{ "field": "topic_def", "operator": "!=", "value": "" }]
      }
    }
  ]
}
```

---

## Artisan Commands

```bash
# Scaffold a new custom field type
php artisan livewire-form-builder:make-field MyType

# Publish migration + Eloquent repository stubs
php artisan livewire-form-builder:publish-stubs

# Publish config
php artisan vendor:publish --tag=livewire-form-builder-config

# Publish views (to customise)
php artisan vendor:publish --tag=livewire-form-builder-views
```

---

## License

MIT

