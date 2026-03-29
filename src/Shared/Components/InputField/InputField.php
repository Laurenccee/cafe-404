<?php
namespace App\Shared\Components\InputField;

class InputField
{
    public static function render($props = [])
    {
        $leading = $props['leading'] ?? null;
        $type = $props['type'] ?? 'text';
        $name = $props['name'] ?? '';
        $label = $props['label'] ?? '';
        $placeholder = $props['placeholder'] ?? '';
        $id = $props['id'] ?? '';
        $disabled = $props['disabled'] ?? false;
        $value = $props['value'] ?? '';
        $oninput = $props['oninput'] ?? '';

        $trailing = $props['trailing'] ?? null;

        if ($disabled === true) {
            $disabledClass = "cursor-not-allowed opacity-50";
        }

        if ($type === 'password') {
            $passwordIcon = "<button tabindex='-1' type='button' onclick='const input = this.previousElementSibling; const isPassword = input.getAttribute(\"type\") === \"password\"; input.setAttribute(\"type\", isPassword ? \"text\" : \"password\"); this.innerHTML = isPassword ? `<i data-lucide=\"eye-closed\"></i>` : `<i data-lucide=\"eye\"></i>`; lucide.createIcons();'><i data-lucide='eye'></i></button>";
        }

        return "
            <inputField class='flex w-full flex-col gap-2.5 '>
                " . ($label ? "<label for='{$id}' class='text-sm opacity-70'>{$label}</label>" : '') . "
                <div class='flex border-2 h-11 border-secondary bg-background rounded-md items-center py-2 px-4 gap-2 " . ($disabledClass ?? '') . "'>
                    " . ($leading ? "<button tabindex='-1' type='button'><i data-lucide='{$leading}' class='leading-icon size-5'></i></button>" : '') . "
                    <input id='{$id}' value='{$value}' type='{$type}' " . ($disabled ? "disabled" : "") . " name='{$name}' class='outline-none w-full text-sm ' placeholder='{$placeholder}' oninput='{$oninput}'>
                    " . ($type === 'password' ? $passwordIcon : '') . "
                    " . ($trailing ? "<button tabindex='-1' type='button'><i data-lucide='{$trailing}' class='trailing-icon size-5'></i></button>" : '') . "
                </div>
            </inputField>
        ";
    }
}