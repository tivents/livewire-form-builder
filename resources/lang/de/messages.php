<?php

return [

    // ── Palette ──────────────────────────────────────────────────────────
    'palette.title'           => 'Feldtypen',
    'palette.group.standard'  => 'Standard',
    'palette.group.inputs'    => 'Eingabefelder',
    'palette.group.layout'    => 'Layout',
    'palette.group.advanced'  => 'Erweitert',

    // ── Builder header ───────────────────────────────────────────────────
    'builder.form_name_placeholder' => 'Formularname…',
    'builder.tab.builder'           => 'Builder',
    'builder.tab.preview'           => 'Vorschau',
    'builder.tab.json'              => 'JSON',
    'builder.save'                  => 'Speichern',

    // ── Canvas ───────────────────────────────────────────────────────────
    'builder.canvas.empty'          => 'Felder hierher ziehen oder einen Typ in der Palette anklicken',
    'builder.row.label'             => 'Zeile',
    'builder.row.empty'             => 'Felder hierher ziehen oder die Zeile anklicken, um Felder über die Einstellungen hinzuzufügen',

    // ── Action titles ────────────────────────────────────────────────────
    'builder.action.move_up'        => 'Nach oben',
    'builder.action.move_down'      => 'Nach unten',
    'builder.action.move_left'      => 'Nach links',
    'builder.action.move_right'     => 'Nach rechts',
    'builder.action.delete_row'     => 'Zeile löschen',
    'builder.action.remove_from_row'=> 'Aus Zeile entfernen',
    'builder.action.duplicate'      => 'Duplizieren',
    'builder.action.delete'         => 'Löschen',
    'builder.action.edit'           => 'Bearbeiten',
    'builder.action.remove'         => 'Entfernen',

    // ── Preview tab ──────────────────────────────────────────────────────
    'builder.preview.untitled'      => 'Unbenanntes Formular',

    // ── JSON tab ─────────────────────────────────────────────────────────
    'builder.json.copy'             => 'JSON kopieren',
    'builder.json.import'           => 'JSON importieren',

    // ── Form settings sidebar ────────────────────────────────────────────
    'builder.form_settings.title'                => 'Formular-Einstellungen',
    'builder.form_settings.description'          => 'Beschreibung',
    'builder.form_settings.description_placeholder' => 'Optionale Beschreibung…',
    'builder.form_settings.active'               => 'Aktiv',
    'builder.form_settings.button_label'         => 'Beschriftung des Senden-Buttons',
    'builder.form_settings.button_label_placeholder' => 'Absenden',
    'builder.form_settings.button_position'      => 'Position des Senden-Buttons',
    'builder.form_settings.button_color'         => 'Farbe des Senden-Buttons',
    'builder.form_settings.align.left'           => 'Links',
    'builder.form_settings.align.center'         => 'Zentriert',
    'builder.form_settings.align.right'          => 'Rechts',

    // ── Field settings modal ─────────────────────────────────────────────
    'builder.field_settings.title'  => 'Feld-Einstellungen',

    // ── Settings panel: Basic ────────────────────────────────────────────
    'settings.basic'                    => 'Allgemein',
    'settings.label'                    => 'Bezeichnung',
    'settings.field_key'                => 'Feld-Key',
    'settings.field_key_unique'         => '(eindeutig)',
    'settings.field_key_placeholder'    => 'Feldbezeichnung',
    'settings.field_key_input_placeholder' => 'feld_key',
    'settings.field_key_duplicate'      => 'Dieser Key wird bereits verwendet.',
    'settings.placeholder'              => 'Platzhalter',
    'settings.hint_text'                => 'Hinweistext',
    'settings.required'                 => 'Pflichtfeld',
    'settings.hidden'                   => 'Ausgeblendet',
    'settings.hidden_note'              => '(wird im Renderer nicht angezeigt)',
    'settings.text'                     => 'Text',
    'settings.level'                    => 'Ebene',
    'settings.style'                    => 'Stil',
    'settings.html_content'             => 'HTML-Inhalt',

    // ── Settings panel: Layout ───────────────────────────────────────────
    'settings.layout'                   => 'Layout',
    'settings.column_width'             => 'Spaltenbreite',
    'settings.width.full'               => 'Volle Breite',
    'settings.width.half'               => 'Halb (1/2)',
    'settings.width.one_third'          => 'Ein Drittel (1/3)',
    'settings.width.two_thirds'         => 'Zwei Drittel (2/3)',
    'settings.width.one_quarter'        => 'Ein Viertel (1/4)',
    'settings.width.three_quarters'     => 'Drei Viertel (3/4)',

    // ── Settings panel: Number ───────────────────────────────────────────
    'settings.number'                   => 'Zahl',
    'settings.min'                      => 'Min',
    'settings.max'                      => 'Max',
    'settings.step'                     => 'Schrittweite',

    // ── Settings panel: Toggle ───────────────────────────────────────────
    'settings.toggle_labels'            => 'Toggle-Beschriftungen',
    'settings.on_label'                 => 'Aktiv-Beschriftung',
    'settings.off_label'                => 'Inaktiv-Beschriftung',

    // ── Settings panel: Hidden field ─────────────────────────────────────
    'settings.hidden_field'             => 'Verstecktes Feld',
    'settings.default_value'            => 'Standardwert',
    'settings.hidden_field_note'        => 'Versteckte Felder werden Nutzern nicht angezeigt, ihr Wert wird aber übermittelt.',

    // ── Settings panel: Input type ───────────────────────────────────────
    'settings.input_type'               => 'Eingabetyp',
    'settings.min_length'               => 'Min. Länge',
    'settings.max_length'               => 'Max. Länge',

    // ── Settings panel: Textarea ─────────────────────────────────────────
    'settings.textarea'                 => 'Textarea',
    'settings.rows'                     => 'Zeilen',

    // ── Settings panel: Date / Time ──────────────────────────────────────
    'settings.datetime'                 => 'Datum / Uhrzeit',
    'settings.datetime.date_only'       => 'Nur Datum',
    'settings.datetime.time_only'       => 'Nur Uhrzeit',
    'settings.datetime.datetime'        => 'Datum & Uhrzeit',
    'settings.min_date'                 => 'Min. Datum',
    'settings.max_date'                 => 'Max. Datum',

    // ── Settings panel: File upload ──────────────────────────────────────
    'settings.file_upload'              => 'Datei-Upload',
    'settings.allow_multiple'           => 'Mehrfachauswahl erlauben',
    'settings.max_size_kb'              => 'Max. Größe (KB)',
    'settings.max_files'                => 'Max. Dateien',

    // ── Settings panel: Options ──────────────────────────────────────────
    'settings.options'                  => 'Optionen',
    'settings.options.add'              => '+ Hinzufügen',
    'settings.options.label_column'     => 'Bezeichnung',
    'settings.options.value_column'     => 'Wert',
    'settings.options.label_placeholder'=> 'Bezeichnung',
    'settings.options.value_placeholder'=> 'wert',
    'settings.multi_select'             => 'Mehrfachauswahl',
    'settings.searchable'               => 'Durchsuchbar',
    'settings.inline_layout'            => 'Inline-Layout',

    // ── Settings panel: Row children ─────────────────────────────────────
    'settings.fields_in_row'            => 'Felder in der Zeile',
    'settings.row.no_fields'            => 'Noch keine Felder — aus der Palette ziehen oder Schaltflächen unten verwenden.',

    // ── Settings panel: Repeater ─────────────────────────────────────────
    'settings.repeater'                 => 'Repeater',
    'settings.min_rows'                 => 'Min. Zeilen',
    'settings.max_rows'                 => 'Max. Zeilen',
    'settings.add_button_label'         => 'Beschriftung der Hinzufügen-Schaltfläche',
    'settings.child_fields'             => 'Unterfelder',

    // ── Settings panel: Conditional logic ────────────────────────────────
    'settings.conditions'               => 'Bedingte Logik',
    'settings.conditions.action'        => 'Aktion',
    'settings.conditions.show'          => 'Dieses Feld anzeigen',
    'settings.conditions.hide'          => 'Dieses Feld ausblenden',
    'settings.conditions.logic'         => 'Logik',
    'settings.conditions.and'           => 'Alle Regeln treffen zu (UND)',
    'settings.conditions.or'            => 'Irgendeine Regel trifft zu (ODER)',
    'settings.conditions.pick_field'    => '— Feld auswählen —',
    'settings.conditions.equals'        => 'ist gleich',
    'settings.conditions.not_equals'    => 'ist nicht gleich',
    'settings.conditions.contains'      => 'enthält',
    'settings.conditions.is_empty'      => 'ist leer',
    'settings.conditions.not_empty'     => 'ist nicht leer',
    'settings.conditions.value_placeholder' => 'Wert…',
    'settings.conditions.remove_rule'   => 'Regel entfernen',
    'settings.conditions.add'           => '+ Bedingung hinzufügen',

    // ── Renderer ─────────────────────────────────────────────────────────
    'renderer.hidden_field'             => 'Verstecktes Feld',
    'renderer.submit'                   => 'Absenden',

    // ── Forms index ──────────────────────────────────────────────────────
    'forms.title'                       => 'Formulare',
    'forms.new'                         => 'Neues Formular',
    'forms.empty'                       => 'Noch keine Formulare.',
    'forms.create_first'                => 'Erstes Formular erstellen →',
    'forms.column.name'                 => 'Name',
    'forms.column.fields'               => 'Felder',
    'forms.column.submissions'          => 'Einsendungen',
    'forms.column.status'               => 'Status',
    'forms.status.active'               => 'Aktiv',
    'forms.status.inactive'             => 'Inaktiv',
    'forms.edit'                        => 'Bearbeiten',
    'forms.delete'                      => 'Löschen',
    'forms.delete_confirm'              => 'Dieses Formular wirklich löschen?',

    // ── Submissions index ────────────────────────────────────────────────
    'submissions.back_to_forms'         => '← Formulare',
    'submissions.title_suffix'          => '– Einsendungen',
    'submissions.export_csv'            => 'CSV exportieren',
    'submissions.empty'                 => 'Noch keine Einsendungen.',
    'submissions.column.id'             => '#',
    'submissions.column.submitted_at'   => 'Eingereicht am',
    'submissions.column.ip'             => 'IP',
    'submissions.column.status'         => 'Status',
    'submissions.status.read'           => 'Gelesen',
    'submissions.status.new'            => 'Neu',
    'submissions.view'                  => 'Ansehen',
    'submissions.delete'                => 'Löschen',
    'submissions.delete_confirm'        => 'Diese Einsendung wirklich löschen?',

    // ── Submissions show ─────────────────────────────────────────────────
    'submissions.back'                  => '← Einsendungen',

    // ── Submissions viewer (Livewire component) ──────────────────────────
    'viewer.really_delete'              => 'Diese Einsendung wirklich löschen?',
    'viewer.delete'                     => 'Löschen',
    'viewer.back'                       => '← Zurück',
    'viewer.meta'                       => 'Meta',
    'viewer.form_data'                  => 'Formulardaten',
    'viewer.empty'                      => 'Noch keine Einsendungen.',
    'viewer.column.actions'             => 'Aktionen',
    'viewer.view'                       => 'Ansehen',
    'viewer.prev'                       => '← Zurück',
    'viewer.next'                       => 'Weiter →',
    'viewer.of'                         => 'von',

    // ── Flash / controller messages ──────────────────────────────────────
    'flash.saved'                       => 'Formular erfolgreich gespeichert!',
    'flash.duplicate_keys'              => 'Doppelte Field Keys: :keys',
    'flash.invalid_json'                => 'Ungültiges JSON.',
    'flash.imported'                    => 'Schema importiert.',
    'flash.form_deleted'                => 'Formular gelöscht.',
    'flash.submission_deleted'          => 'Einsendung gelöscht.',

];
