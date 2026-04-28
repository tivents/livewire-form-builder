<?php

return [

    // ── Palette ──────────────────────────────────────────────────────────
    'palette.title'           => 'Field Types',
    'palette.group.standard'  => 'Standard',
    'palette.group.inputs'    => 'Input Fields',
    'palette.group.layout'    => 'Layout',
    'palette.group.advanced'  => 'Advanced',

    // ── Builder header ───────────────────────────────────────────────────
    'builder.form_name_placeholder' => 'Form name…',
    'builder.tab.builder'           => 'Builder',
    'builder.tab.preview'           => 'Preview',
    'builder.tab.json'              => 'JSON',
    'builder.save'                  => 'Save',

    // ── Canvas ───────────────────────────────────────────────────────────
    'builder.canvas.empty'          => 'Drag fields here or click a type in the palette',
    'builder.row.label'             => 'Row',
    'builder.row.empty'             => 'Drop fields here or click the Row to add fields via settings',

    // ── Action titles ────────────────────────────────────────────────────
    'builder.action.move_up'        => 'Move up',
    'builder.action.move_down'      => 'Move down',
    'builder.action.move_left'      => 'Move left',
    'builder.action.move_right'     => 'Move right',
    'builder.action.delete_row'     => 'Delete row',
    'builder.action.remove_from_row'=> 'Remove from row',
    'builder.action.duplicate'      => 'Duplicate',
    'builder.action.delete'         => 'Delete',
    'builder.action.edit'           => 'Edit',
    'builder.action.remove'         => 'Remove',

    // ── Preview tab ──────────────────────────────────────────────────────
    'builder.preview.untitled'      => 'Untitled Form',

    // ── JSON tab ─────────────────────────────────────────────────────────
    'builder.json.copy'             => 'Copy JSON',
    'builder.json.import'           => 'Import JSON',

    // ── Form settings sidebar ────────────────────────────────────────────
    'builder.form_settings.title'                => 'Form Settings',
    'builder.form_settings.description'          => 'Description',
    'builder.form_settings.description_placeholder' => 'Optional description…',
    'builder.form_settings.active'               => 'Active',
    'builder.form_settings.button_label'         => 'Submit button label',
    'builder.form_settings.button_label_placeholder' => 'Submit',
    'builder.form_settings.button_position'      => 'Submit button position',
    'builder.form_settings.button_color'         => 'Submit button color',
    'builder.form_settings.align.left'           => 'Left',
    'builder.form_settings.align.center'         => 'Center',
    'builder.form_settings.align.right'          => 'Right',

    // ── Field settings modal ─────────────────────────────────────────────
    'builder.field_settings.title'  => 'Field Settings',

    // ── Settings panel: Basic ────────────────────────────────────────────
    'settings.basic'                    => 'Basic',
    'settings.label'                    => 'Label',
    'settings.field_key'                => 'Field Key',
    'settings.field_key_unique'         => '(unique)',
    'settings.field_key_placeholder'    => 'Field label',
    'settings.field_key_input_placeholder' => 'field_key',
    'settings.field_key_duplicate'      => 'This key is already in use.',
    'settings.placeholder'              => 'Placeholder',
    'settings.hint_text'                => 'Hint text',
    'settings.required'                 => 'Required',
    'settings.hidden'                   => 'Hidden',
    'settings.hidden_note'              => '(not shown in renderer)',
    'settings.text'                     => 'Text',
    'settings.level'                    => 'Level',
    'settings.style'                    => 'Style',
    'settings.html_content'             => 'HTML Content',

    // ── Settings panel: Layout ───────────────────────────────────────────
    'settings.layout'                   => 'Layout',
    'settings.column_width'             => 'Column Width',
    'settings.width.full'               => 'Full width',
    'settings.width.half'               => 'Half (1/2)',
    'settings.width.one_third'          => 'One Third (1/3)',
    'settings.width.two_thirds'         => 'Two Thirds (2/3)',
    'settings.width.one_quarter'        => 'One Quarter (1/4)',
    'settings.width.three_quarters'     => 'Three Quarters (3/4)',

    // ── Settings panel: Number ───────────────────────────────────────────
    'settings.number'                   => 'Number',
    'settings.min'                      => 'Min',
    'settings.max'                      => 'Max',
    'settings.step'                     => 'Step',

    // ── Settings panel: Toggle ───────────────────────────────────────────
    'settings.toggle_labels'            => 'Toggle Labels',
    'settings.on_label'                 => 'On label',
    'settings.off_label'                => 'Off label',

    // ── Settings panel: Hidden field ─────────────────────────────────────
    'settings.hidden_field'             => 'Hidden Field',
    'settings.default_value'            => 'Default value',
    'settings.hidden_field_note'        => 'Hidden fields are not shown to users but their value is submitted.',

    // ── Settings panel: Input type ───────────────────────────────────────
    'settings.input_type'               => 'Input Type',
    'settings.min_length'               => 'Min length',
    'settings.max_length'               => 'Max length',

    // ── Settings panel: Textarea ─────────────────────────────────────────
    'settings.textarea'                 => 'Textarea',
    'settings.rows'                     => 'Rows',

    // ── Settings panel: Date / Time ──────────────────────────────────────
    'settings.datetime'                 => 'Date / Time',
    'settings.datetime.date_only'       => 'Date only',
    'settings.datetime.time_only'       => 'Time only',
    'settings.datetime.datetime'        => 'Date & Time',
    'settings.min_date'                 => 'Min date',
    'settings.max_date'                 => 'Max date',

    // ── Settings panel: File upload ──────────────────────────────────────
    'settings.file_upload'              => 'File Upload',
    'settings.allow_multiple'           => 'Allow multiple',
    'settings.max_size_kb'              => 'Max size (KB)',
    'settings.max_files'                => 'Max files',

    // ── Settings panel: Options ──────────────────────────────────────────
    'settings.options'                  => 'Options',
    'settings.options.add'              => '+ Add',
    'settings.options.label_column'     => 'Label',
    'settings.options.value_column'     => 'Value',
    'settings.options.label_placeholder'=> 'Label',
    'settings.options.value_placeholder'=> 'value',
    'settings.multi_select'             => 'Multi-select',
    'settings.searchable'               => 'Searchable',
    'settings.inline_layout'            => 'Inline layout',

    // ── Settings panel: Row children ─────────────────────────────────────
    'settings.fields_in_row'            => 'Fields in Row',
    'settings.row.no_fields'            => 'No fields yet — drag from the palette or use buttons below.',

    // ── Settings panel: Repeater ─────────────────────────────────────────
    'settings.repeater'                 => 'Repeater',
    'settings.min_rows'                 => 'Min rows',
    'settings.max_rows'                 => 'Max rows',
    'settings.add_button_label'         => 'Add button label',
    'settings.child_fields'             => 'Child fields',

    // ── Settings panel: Conditional logic ────────────────────────────────
    'settings.conditions'               => 'Conditional Logic',
    'settings.conditions.action'        => 'Action',
    'settings.conditions.show'          => 'Show this field',
    'settings.conditions.hide'          => 'Hide this field',
    'settings.conditions.logic'         => 'Logic',
    'settings.conditions.and'           => 'All rules match (AND)',
    'settings.conditions.or'            => 'Any rule matches (OR)',
    'settings.conditions.pick_field'    => '— pick field —',
    'settings.conditions.equals'        => 'equals',
    'settings.conditions.not_equals'    => 'not equals',
    'settings.conditions.contains'      => 'contains',
    'settings.conditions.is_empty'      => 'is empty',
    'settings.conditions.not_empty'     => 'is not empty',
    'settings.conditions.value_placeholder' => 'Value…',
    'settings.conditions.remove_rule'   => 'Remove rule',
    'settings.conditions.add'           => '+ Add condition',

    // ── Renderer ─────────────────────────────────────────────────────────
    'renderer.hidden_field'             => 'Hidden field',
    'renderer.submit'                   => 'Submit',

    // ── Forms index ──────────────────────────────────────────────────────
    'forms.title'                       => 'Forms',
    'forms.new'                         => 'New Form',
    'forms.empty'                       => 'No forms yet.',
    'forms.create_first'                => 'Create your first form →',
    'forms.column.name'                 => 'Name',
    'forms.column.fields'               => 'Fields',
    'forms.column.submissions'          => 'Submissions',
    'forms.column.status'               => 'Status',
    'forms.status.active'               => 'Active',
    'forms.status.inactive'             => 'Inactive',
    'forms.edit'                        => 'Edit',
    'forms.delete'                      => 'Delete',
    'forms.delete_confirm'              => 'Delete this form?',

    // ── Submissions index ────────────────────────────────────────────────
    'submissions.back_to_forms'         => '← Forms',
    'submissions.title_suffix'          => '– Submissions',
    'submissions.export_csv'            => 'Export CSV',
    'submissions.empty'                 => 'No submissions yet.',
    'submissions.column.id'             => '#',
    'submissions.column.submitted_at'   => 'Submitted At',
    'submissions.column.ip'             => 'IP',
    'submissions.column.status'         => 'Status',
    'submissions.status.read'           => 'Read',
    'submissions.status.new'            => 'New',
    'submissions.view'                  => 'View',
    'submissions.delete'                => 'Delete',
    'submissions.delete_confirm'        => 'Delete this submission?',

    // ── Submissions show ─────────────────────────────────────────────────
    'submissions.back'                  => '← Submissions',

    // ── Submissions viewer (Livewire component) ──────────────────────────
    'viewer.really_delete'              => 'Really delete this submission?',
    'viewer.delete'                     => 'Delete',
    'viewer.back'                       => '← Back',
    'viewer.meta'                       => 'Meta',
    'viewer.form_data'                  => 'Form Data',
    'viewer.empty'                      => 'No submissions yet.',
    'viewer.column.actions'             => 'Actions',
    'viewer.view'                       => 'View',
    'viewer.prev'                       => '← Prev',
    'viewer.next'                       => 'Next →',
    'viewer.of'                         => 'of',

    // ── Flash / controller messages ──────────────────────────────────────
    'flash.saved'                       => 'Form saved successfully!',
    'flash.duplicate_keys'              => 'Duplicate field keys: :keys',
    'flash.invalid_json'                => 'Invalid JSON.',
    'flash.imported'                    => 'Schema imported.',
    'flash.form_deleted'                => 'Form deleted.',
    'flash.submission_deleted'          => 'Submission deleted.',

];
