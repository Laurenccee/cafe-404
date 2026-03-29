<?php

namespace App\Shared\Components;

class TextArea
{
    public static function render($props = [])
    {
        $className = $props['class'] ?? '';
        $name = $props['name'] ?? '';
        $label = $props['label'] ?? '';
        $placeholder = $props['placeholder'] ?? '';
        $value = $props['value'] ?? '';
        $rows = $props['rows'] ?? 4;
        $required = ($props['required'] ?? false) ? 'required' : '';

        return "
            <div class='flex flex-col gap-2 w-full'>
                <label for='{$name}' class='font-sans-serif text-sm opacity-70'>
                    " . htmlspecialchars($label) . "
                </label>
                <textarea 
                    id='{$name}' 
                    name='{$name}' 
                    rows='{$rows}'
                    placeholder='" . htmlspecialchars($placeholder) . "'
                    class='w-full py-2 px-4 bg-background border border-2 border-secondary rounded-md outline-none text-sm {$className}'
                    {$required}
                >" . htmlspecialchars($value) . "</textarea>
            </div>
        ";
    }
}