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

        $sizes = [
            'sm' => 'h-8 px-3 py-2 text-xs',
            'md' => 'h-11 px-6 py-2 text-sm',
            'lg' => 'h-14 px-8 py-3 text-base',
        ];

        $variants = [
            'primary' => 'bg-[#2d5a27] text-white hover:opacity-90', // Deep Matcha
            'secondary' => 'bg-[#F0EDED] text-[#2c2c2c] hover:opacity-90', // Roasted Bean
            'tertiary' => 'bg-[#6f4e37] text-[#e4e4cc]',      // Ghost/Link style
            'ghost' => 'bg-transparent text-[#002c02] underline',      // Ghost/Link style
            'outline' => 'border-2 border-[#002c02] text-[#002c02] hover:bg-[#002c02] hover:text-white'
        ];

        $variant = $props['variant'] ?? 'primary';
        $variantClasses = $variants[$variant] ?? $variants['primary'];

        $tagName = $href ? 'a' : 'button';
        $attributes = $href ? "href='{$href}'" : "type='submit'";

        return "
        <{$tagName} {$attributes} onclick=\"{$onclick}\" class='flex w-full justify-center hover:opacity-90 items-center rounded-md gap-2 " . ($sizes[$size] ?? $sizes['md']) . " transition-all duration-300 {$variantClasses} {$class}'>
            " . ($leading ? "<i data-lucide='{$leading}' class='size-4'></i>" : "") . "
            <span class='font-sans font-bold uppercase tracking-widest'>{$text}</span>
            " . ($trailing ? "<i data-lucide='{$trailing}' class='size-4'></i>" : "") . "
        </{$tagName}>
        ";


    }
}