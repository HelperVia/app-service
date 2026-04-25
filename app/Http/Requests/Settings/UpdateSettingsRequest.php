<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }


    private function validateFormFields($validator, array $fields, string $prefix): void
    {
        $optionRequiredTypes = ['choice_list', 'multiple_choice_list', 'dropdown'];
        $uniqueTypes = ['name', 'email', 'chat_rating', 'thank_u_message'];
        $ids = [];
        $types = [];

        foreach ($fields as $index => $field) {

            if (isset($field['id'])) {
                if (in_array($field['id'], $ids)) {
                    $validator->errors()->add("{$prefix}.fields.{$index}.id", "Field ID must be unique.");
                } else {
                    $ids[] = $field['id'];
                }
            }


            if (in_array($field['type'] ?? '', $uniqueTypes)) {
                if (in_array($field['type'], $types)) {
                    $validator->errors()->add("{$prefix}.fields.{$index}.type", "Only one {$field['type']} field is allowed.");
                } else {
                    $types[] = $field['type'];
                }
            }


            if (in_array($field['type'] ?? '', $optionRequiredTypes)) {
                if (empty($field['options'])) {
                    $validator->errors()->add("{$prefix}.fields.{$index}.options", "Options are required for {$field['type']} type.");
                }
            }


            if (in_array($field['type'] ?? '', $optionRequiredTypes) && !empty($field['options'])) {
                $optionIds = [];
                foreach ($field['options'] as $optIndex => $option) {
                    if (isset($option['id'])) {
                        if (in_array($option['id'], $optionIds)) {
                            $validator->errors()->add("{$prefix}.fields.{$index}.options.{$optIndex}.id", "Option ID must be unique.");
                        } else {
                            $optionIds[] = $option['id'];
                        }
                    }
                }
            }


            if ((($field['type'] ?? '') !== 'information' && ($field['type'] ?? '') !== 'thank_u_message') && empty($field['label'])) {
                $validator->errors()->add("{$prefix}.fields.{$index}.label", "Label is required.");
            }


            if (isset($field['order']) && !is_int($field['order'])) {
                $validator->errors()->add("{$prefix}.fields.{$index}.order", "Order must be an integer, not a string.");
            }


            if (($field['type'] ?? '') === 'information') {
                if (empty($field['content'])) {
                    $validator->errors()->add("{$prefix}.fields.{$index}.content", "Content is required for information type.");
                } elseif (strlen(strip_tags($field['content'])) > 500) {
                    $validator->errors()->add("{$prefix}.fields.{$index}.content", "Content must not exceed 500 characters.");
                }
            }

            if (($field['type'] ?? '') === 'thank_u_message') {
                if (empty($field['content'])) {
                    $validator->errors()->add("{$prefix}.fields.{$index}.content", "Content is required for Thank You Message type.");
                } elseif (strlen(strip_tags($field['content'])) > 500) {
                    $validator->errors()->add("{$prefix}.fields.{$index}.content", "Content must not exceed 500 characters.");
                }
            }

        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('prechatform.fields')) {
                $this->validateFormFields(
                    $validator,
                    $this->input('prechatform.fields', []),
                    'prechatform'
                );
            }

            if ($this->has('postchatform.fields')) {
                $this->validateFormFields(
                    $validator,
                    $this->input('postchatform.fields', []),
                    'postchatform'
                );
            }

            if ($this->has('widget_customization')) {
                $colorFields = [
                    'widget_customization.appearance.themeDetail.minimized.widget_bg',
                    'widget_customization.appearance.themeDetail.minimized.widget_text',
                    'widget_customization.appearance.themeDetail.minimized.widget_icon_color',
                    'widget_customization.appearance.themeDetail.maximized.chat_bg',
                    'widget_customization.appearance.themeDetail.maximized.primary_color',
                    'widget_customization.appearance.themeDetail.maximized.customer_bubble',
                    'widget_customization.appearance.themeDetail.maximized.customer_bubble_text',
                    'widget_customization.appearance.themeDetail.maximized.agent_bubble',
                    'widget_customization.appearance.themeDetail.maximized.agent_bubble_text',
                    'widget_customization.appearance.themeDetail.maximized.system_messages',
                ];

                foreach ($colorFields as $field) {
                    $value = $this->input($field);
                    if ($value && !preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) {
                        $validator->errors()->add($field, "Invalid hex color format.");
                    }
                }

                $themeColor = $this->input('widget_customization.appearance.themeColor');
                if ($themeColor && !preg_match('/^#[0-9A-Fa-f]{6}$/', $themeColor)) {
                    $validator->errors()->add('widget_customization.appearance.themeColor', "Invalid hex color format.");
                }
            }
        });
    }


    protected function passedValidation()
    {
        foreach (['prechatform', 'postchatform'] as $form) {
            $fields = $this->input("{$form}.fields", []);
            if (empty($fields))
                continue;

            $cleaned = array_map(function ($field) {
                if (($field['type'] === 'information' || $field['type'] === 'thank_u_message') && isset($field['content'])) {
                    $field['content'] = strip_tags($field['content'], '<b><i><u><a><br>');
                }
                return $field;
            }, $fields);

            $this->merge([
                $form => array_merge($this->input($form, []), ['fields' => $cleaned]),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $formRules = [];
        foreach (['prechatform', 'postchatform'] as $form) {
            $formRules = array_merge($formRules, [
                "{$form}" => ['sometimes', 'array'],
                "{$form}.enabled" => ['required_with:' . $form, 'boolean'],
                "{$form}.fields" => ['sometimes', 'array'],
                "{$form}.fields.*.id" => ['required', 'string'],
                "{$form}.fields.*.label" => ['nullable', 'string'],
                "{$form}.fields.*.name" => ['required', 'string'],
                "{$form}.fields.*.order" => ['required', 'numeric'],
                "{$form}.fields.*.required" => ['required', 'boolean'],
                "{$form}.fields.*.deletable" => ['required', 'boolean'],
                "{$form}.fields.*.type" => [
                    'required',
                    'string',
                    Rule::in([
                        'name',
                        'email',
                        'question',
                        'choice_list',
                        'multiple_choice_list',
                        'dropdown',
                        'information',
                        'number',
                        'password',
                        'thank_u_message',
                        'chat_rating'
                    ])
                ],
                "{$form}.fields.*.options" => ['nullable', 'array'],
                "{$form}.fields.*.options.*.id" => ['required_with:' . "{$form}.fields.*.options", 'string', 'min:1'],
                "{$form}.fields.*.options.*.value" => ['required_with:' . "{$form}.fields.*.options", 'string', 'min:1'],
                "{$form}.fields.*.content" => ['nullable', 'string'],
            ]);
        }


        $widgetRules = [
            'widget_customization' => ['sometimes', 'array'],
            'widget_customization.appearance' => ['sometimes', 'array'],
            'widget_customization.appearance.initial' => ['required_with:widget_customization', 'string', Rule::in(['minimized', 'maximized'])],
            'widget_customization.appearance.window' => ['required_with:widget_customization', 'string', Rule::in(['bar', 'bubble'])],
            'widget_customization.appearance.theme' => ['required_with:widget_customization', 'string', Rule::in(['light', 'dark'])],
            'widget_customization.appearance.themeColor' => ['nullable', 'string'],
            'widget_customization.appearance.themeDetail.minimized.widget_bg' => ['required_with:widget_customization', 'string'],
            'widget_customization.appearance.themeDetail.minimized.widget_text' => ['required_with:widget_customization', 'string'],
            'widget_customization.appearance.themeDetail.minimized.widget_icon_color' => ['required_with:widget_customization', 'string'],
            'widget_customization.appearance.themeDetail.minimized.gradient' => ['required_with:widget_customization', 'boolean'],
            'widget_customization.appearance.themeDetail.maximized.chat_bg' => ['required_with:widget_customization', 'string'],
            'widget_customization.appearance.themeDetail.maximized.primary_color' => ['required_with:widget_customization', 'string'],
            'widget_customization.appearance.themeDetail.maximized.customer_bubble' => ['required_with:widget_customization', 'string'],
            'widget_customization.appearance.themeDetail.maximized.customer_bubble_text' => ['required_with:widget_customization', 'string'],
            'widget_customization.appearance.themeDetail.maximized.agent_bubble' => ['required_with:widget_customization', 'string'],
            'widget_customization.appearance.themeDetail.maximized.agent_bubble_text' => ['required_with:widget_customization', 'string'],
            'widget_customization.appearance.themeDetail.maximized.system_messages' => ['required_with:widget_customization', 'string'],
            'widget_customization.appearance.themeDetail.maximized.gradient' => ['required_with:widget_customization', 'boolean'],
            'widget_customization.position.align' => ['required_with:widget_customization', 'string', Rule::in(['left', 'right'])],
            'widget_customization.position.side_spacing' => ['required_with:widget_customization', 'integer', 'min:0', 'max:200'],
            'widget_customization.position.bottom_spacing' => ['required_with:widget_customization', 'integer', 'min:0', 'max:200'],
            'widget_customization.visibility' => ['required_with:widget_customization', 'string', Rule::in(['visible', 'hidden', 'proactive_only'])],
            'widget_customization.advanced.show_logo' => ['required_with:widget_customization', 'boolean'],
            'widget_customization.advanced.show_agent_photo' => ['required_with:widget_customization', 'boolean'],
            'widget_customization.advanced.show_rate_agent' => ['required_with:widget_customization', 'boolean'],
            'widget_customization.advanced.show_powered_by' => ['required_with:widget_customization', 'boolean'],
            'widget_customization.advanced.show_typing_indicator' => ['required_with:widget_customization', 'boolean'],
            'widget_customization.advanced.sound_enabled' => ['required_with:widget_customization', 'boolean'],
        ];

        $widgetLanguageRules = [
            'widget_language' => ['sometimes', 'array'],
            'widget_language.language' => ['required_with:widget_language', 'string', Rule::in(['en', 'tr'])],
            'widget_language.translations' => ['required_with:widget_language', 'array'],
            'widget_language.translations.welcome_message' => ['required_with:widget_language', 'string', 'min:1'],
            'widget_language.translations.customer_name' => ['required_with:widget_language', 'string', 'min:1'],
            'widget_language.translations.message_placeholder' => ['required_with:widget_language', 'string', 'min:1'],
            'widget_language.translations.offline_info' => ['required_with:widget_language', 'string', 'min:1'],
            'widget_language.translations.queued_customer_info' => ['required_with:widget_language', 'string', 'min:1'],
        ];

        return array_merge($formRules, $widgetRules, $widgetLanguageRules);
    }

    public function messages(): array
    {
        $messages = [];
        foreach (['prechatform', 'postchatform'] as $form) {
            $messages = array_merge($messages, [
                "{$form}.fields.*.type.in" => 'Invalid field type.',
                "{$form}.fields.*.id.required" => 'Field ID is required.',
                "{$form}.fields.*.label.required" => 'Field label is required.',
                "{$form}.fields.*.order.numeric" => 'Field order must be a number.',
            ]);
        }

        $messages = array_merge($messages, [
            'widget_language.language.in' => 'Invalid language code.',
            'widget_language.translations.welcome_message.required_with' => 'Welcome message is required.',
            'widget_language.translations.customer_name.required_with' => 'Customer name is required.',
            'widget_language.translations.message_placeholder.required_with' => 'Message placeholder is required.',
            'widget_language.translations.offline_info.required_with' => 'Offline info is required.',
            'widget_language.translations.queued_customer_info.required_with' => 'Queued customer info is required.',
        ]);
        return $messages;
    }
}
