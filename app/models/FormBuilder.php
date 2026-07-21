<?php

class FormBuilder
{
    /**
     * Generate a text input field
     */
    public static function text(string $name, string $value = '', string $label = '', array $attributes = []): string
    {
        $id = $attributes['id'] ?? $name;
        $class = $attributes['class'] ?? 'form-control';
        $placeholder = $attributes['placeholder'] ?? '';
        $required = isset($attributes['required']) ? 'required' : '';

        return "
            <div class=\"mb-3\">
                <label for=\"$id\" class=\"form-label\">$label</label>
                <input type=\"text\" name=\"$name\" id=\"$id\" class=\"$class\" placeholder=\"$placeholder\" value=\"" . esc($value) . "\" $required>
            </div>
        ";
    }

    /**
     * Generate a number input field
     */
    public static function number(string $name, string $value = '', string $label = '', array $attributes = []): string
    {
        $id = $attributes['id'] ?? $name;
        $class = $attributes['class'] ?? 'form-control';
        $placeholder = $attributes['placeholder'] ?? '';
        $step = $attributes['step'] ?? '0.01';
        $min = $attributes['min'] ?? '0';
        $required = isset($attributes['required']) ? 'required' : '';

        return "
            <div class=\"mb-3\">
                <label for=\"$id\" class=\"form-label\">$label</label>
                <input type=\"number\" step=\"$step\" min=\"$min\" name=\"$name\" id=\"$id\" class=\"$class\" placeholder=\"$placeholder\" value=\"" . esc($value) . "\" $required>
            </div>
        ";
    }

    /**
     * Generate a textarea field
     */
    public static function textarea(string $name, string $value = '', string $label = '', array $attributes = []): string
    {
        $id = $attributes['id'] ?? $name;
        $class = $attributes['class'] ?? 'form-control';
        $rows = $attributes['rows'] ?? 3;
        $required = isset($attributes['required']) ? 'required' : '';

        return "
            <div class=\"mb-3\">
                <label for=\"$id\" class=\"form-label\">$label</label>
                <textarea name=\"$name\" id=\"$id\" class=\"$class\" rows=\"$rows\" $required>" . esc($value) . "</textarea>
            </div>
        ";
    }

    /**
     * Generate a select dropdown
     */
    public static function select(string $name, array $options = [], string $selected = '', string $label = '', array $attributes = []): string
    {
        $id = $attributes['id'] ?? $name;
        $class = $attributes['class'] ?? 'form-control';
        $required = isset($attributes['required']) ? 'required' : '';

        $html = "
            <div class=\"mb-3\">
                <label for=\"$id\" class=\"form-label\">$label</label>
                <select name=\"$name\" id=\"$id\" class=\"$class\" $required>";

        foreach ($options as $value => $label) {
            $is_selected = ($value == $selected) ? ' selected' : '';
            $html .= "<option value=\"$value\"$is_selected>$label</option>";
        }

        $html .= "</select></div>";

        return $html;
    }

    /**
     * Generate a hidden input
     */
    public static function hidden(string $name, string $value = ''): string
    {
        return "<input type=\"hidden\" name=\"$name\" value=\"" . esc($value) . "\">";
    }

    /**
     * Generate a file upload input
     */
    public static function file(string $name, string $label = '', array $attributes = []): string
    {
        $id = $attributes['id'] ?? $name;
        $class = $attributes['class'] ?? 'form-control';
        $required = isset($attributes['required']) ? 'required' : '';

        return "
            <div class=\"mb-3\">
                <label for=\"$id\" class=\"form-label\">$label</label>
                <input type=\"file\" name=\"$name\" id=\"$id\" class=\"$class\" $required>
            </div>
        ";
    }

    /**
     * Generate a submit button
     */
    public static function submit(string $label = 'Submit', string $class = 'btn btn-warning text-dark'): string
    {
        return "<button type=\"submit\" class=\"$class\">$label</button>";
    }
}