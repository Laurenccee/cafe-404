<?php

namespace App\Shared\Components\ComboBox;

class ComboBoxField
{
    public static function render($props = [])
    {
        $leading = $props['leading'] ?? 'layers';
        $name = $props['name'] ?? '';
        $label = $props['label'] ?? '';
        $options = $props['options'] ?? [];
        $selected = $props['selected'] ?? $props['value'] ?? '';
        $placeholder = $props['placeholder'] ?? 'Select an option';

        $id = "combo_" . uniqid();

        $initialLabel = $placeholder;
        foreach ($options as $opt) {
            if ($opt['value'] == $selected) {
                $initialLabel = $opt['label'];
                break;
            }
        }

        $optionsHtml = "";
        foreach ($options as $option) {
            $optionsHtml .= "
                <div class='custom-option hover:bg-[#F0EDED] px-4 py-2 text-sm cursor-pointer transition-colors' 
                     data-value='{$option['value']}' 
                     data-label='{$option['label']}'>
                    {$option['label']}
                </div>";
        }

        return "
            <div class='flex flex-col gap-2.5 w-full text-sm relative custom-combobox' id='{$id}'>
                " . ($label ? "<label class='font-sans-serif text-sm opacity-70'>{$label}</label>" : "") . "
                
                <div class='combo-trigger flex border-2 h-11 border-secondary bg-background rounded-md items-center py-2 px-4 gap-2 cursor-pointer focus-within:border-primary transition-all'>
                    " . ($leading ? "<i data-lucide='{$leading}' class='leading-icon size-5'></i>" : '') . "
                    <span class='selected-text font-sans-serif capitalize flex-1 text-sm'>{$initialLabel}</span>
                    <i data-lucide='chevron-down' class='chevron size-4 transition-transform'></i>
                </div>

                <input type='hidden' name='{$name}' value='{$selected}' class='combo-input placeholder='{$placeholder}''>

                <div class='combo-menu hidden absolute top-[105%] left-0 w-full bg-background border-2 border-secondary rounded-md shadow-lg z-50 overflow-hidden'>
                    <div class='flex flex-col max-h-60 capitalize overflow-y-auto'>
                        {$optionsHtml}
                    </div>
                </div>
            </div>

            <script>
                (function() {
                    const combo = document.getElementById('{$id}');
                    const trigger = combo.querySelector('.combo-trigger');
                    const menu = combo.querySelector('.combo-menu');
                    const input = combo.querySelector('.combo-input');
                    const text = combo.querySelector('.selected-text');
                    const chevron = combo.querySelector('.chevron');

                    trigger.addEventListener('click', () => {
                        const isOpen = !menu.classList.contains('hidden');
                        document.querySelectorAll('.combo-menu').forEach(m => m.classList.add('hidden')); // Close others
                        if (!isOpen) {
                            menu.classList.remove('hidden');
                            chevron.style.transform = 'rotate(180deg)';
                        } else {
                            menu.classList.add('hidden');
                            chevron.style.transform = 'rotate(0deg)';
                        }
                    });

                    combo.querySelectorAll('.custom-option').forEach(opt => {
                        opt.addEventListener('click', (e) => {
                            const val = opt.getAttribute('data-value');
                            const lab = opt.getAttribute('data-label');
                            input.value = val;
                            text.innerText = lab;
                            menu.classList.add('hidden');
                            chevron.style.transform = 'rotate(0deg)';
                        });
                    });

                    document.addEventListener('click', (e) => {
                        if (!combo.contains(e.target)) {
                            menu.classList.add('hidden');
                            chevron.style.transform = 'rotate(0deg)';
                        }
                    });
                })();
            </script>
        ";
    }
}