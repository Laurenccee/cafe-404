<?php

namespace App\Shared\Components;

class FileDrop
{
    public static function render($name, $existingImage = null, $label = "Product Image", $props = [])
    {
        $id = $props['id'] ?? $name;
        $accept = $props['accept'] ?? 'image/*';
        $initX = $props['init_x'] ?? 50;
        $initY = $props['init_y'] ?? 50;

        $hasImage = !empty($existingImage);
        $previewClass = $hasImage ? "" : "hidden";
        $promptClass = $hasImage ? "hidden" : "";

        return "
            <div class='flex flex-col gap-2 w-full'>
                " . ($label ? "<label for='{$id}' class='text-[10px] font-medium opacity-60 uppercase tracking-widest'>{$label}</label>" : '') . "
                
                <div id='drop-zone-{$id}' 
                     class='relative group flex flex-col bg-[#F0EDED] items-center justify-center w-full aspect-square border-2 border-dashed border-secondary rounded-2xl hover:bg-slate-50 hover:border-primary/50 transition-all cursor-pointer overflow-hidden'>
                    
                    <input type='file' name='{$name}' id='{$id}' class='hidden' accept='{$accept}'>
                    
                    <input type='hidden' name='{$name}_pos_y' id='pos-y-{$id}' value='50'>
                    
                    <div id='preview-container-{$id}' class='absolute inset-0 {$previewClass} cursor-ns-resize select-none z-20 pointer-events: auto;'>
                        <img id='preview-img-{$id}' 
                        src='{$existingImage}'
                             class='w-full h-full object-cover transition-transform duration-75' 
                             style='object-position: {$initX}% {$initY}%;'>
                        <input type='hidden' name='{$name}_pos_x' id='pos-x-{$id}' value='{$initX}'>
                        <input type='hidden' name='{$name}_pos_y' id='pos-y-{$id}' value='{$initY}'>
                        
                        <div class='absolute bottom-3 left-1/2 -translate-x-1/2 bg-black/40 backdrop-blur-md text-white text-[9px] px-3 py-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity uppercase tracking-widest pointer-events-none'>
                            Drag to Frame
                        </div>
                    </div>

                    <div id='prompt-{$id}' class='flex flex-col items-center gap-3 z-10 pointer-events-none {$promptClass}'>
                        <div class='text-center'>
                            <p class='text-xs font-bold text-title'>Click to upload or drag and drop</p>
                            <p class='text-xs text-subtitle font-bold opacity-50 uppercase tracking-tighter'>PNG, JPG or WebP (Max. 2MB)</p>
                        </div>
                    </div>
                </div>
            </div>
        ";
    }
}