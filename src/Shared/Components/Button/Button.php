<?php
namespace App\Shared\Components\Button;

class Button
{
    public static function render($text, $props = [])
    {
        $leading = $props['leading'] ?? null;
        $trailing = $props['trailing'] ?? null;
        $href = $props['href'] ?? null;
        $class = $props['class'] ?? '';
        $size = $props['size'] ?? 'md';
        $onclick = $props['onclick'] ?? '';
        $type = $props['type'] ?? 'submit';
        
        $availability = strtolower($props['availability'] ?? ''); 
        $isDisabled = ($availability !== 'available' && $availability !== '' || ($props['disabled'] ?? false));

        $sizes = [
            'sm' => 'h-8 px-3 py-2 text-xs',
            'md' => 'h-11 px-6 py-2 text-sm',
            'lg' => 'h-14 px-8 py-3 text-base',
        ];

        // Standard Theme Variants
        $variants = [
            'primary'   => 'bg-[#2d5a27] text-white hover:opacity-90',
            'secondary' => 'bg-[#F0EDED] text-[#2c2c2c] hover:opacity-90',
            'tertiary'  => 'bg-[#6f4e37] text-[#e4e4cc]',
            'ghost'     => 'bg-transparent text-[#002c02] underline',
            'outline'   => 'border-2 border-[#002c02] text-[#002c02] hover:bg-[#002c02] hover:text-white'
        ];

        // Specific Status Overrides
        $statusOverrides = [
            'available'     => 'bg-emerald-600 text-white hover:bg-emerald-700',
            'not available' => 'bg-rose-500 text-white cursor-not-allowed opacity-40',
            'sold out'      => 'bg-amber-500 text-white cursor-not-allowed opacity-40',
        ];

        // LOGIC: Use status override if it exists, otherwise use the variant
        $variant = $props['variant'] ?? 'primary';
        $variantClasses = $statusOverrides[$availability] ?? ($variants[$variant] ?? $variants['primary']);

        $tagName = $href ? 'a' : 'button';
        $disabledAttr = $isDisabled ? 'disabled' : '';
        $attributes = $href ? "href='{$href}'" : "type='{$type}'";

        $displayText = ($isDisabled) ? strtoupper($availability) : $text;

        return "
        <{$tagName} {$attributes} {$disabledAttr} onclick=\"{$onclick}\" 
            class='flex w-full justify-center items-center rounded-md gap-2 " . ($sizes[$size] ?? $sizes['md']) . " 
            transition-all duration-300 {$variantClasses} {$class}'>
            " . ($leading ? "<i data-lucide='{$leading}' class='size-4'></i>" : "") . "
            <span class='font-sans font-bold uppercase tracking-widest'>{$displayText}</span>
            " . ($trailing ? "<i data-lucide='{$trailing}' class='size-4'></i>" : "") . "
        </{$tagName}>
        ";
    }
}