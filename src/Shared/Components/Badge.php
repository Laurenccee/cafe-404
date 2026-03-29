<?php

namespace App\Shared\Components;

class Badge
{
    public static function render($label, $roleOrProps = 2, $props = [])
    {

        // If the second argument is an array, treat it as the $props
        if (is_array($roleOrProps)) {
            $props = $roleOrProps;
            $roleId = null;
        } else {
            $roleId = $roleOrProps;
        }

        $className = $props['class'] ?? '';

        $colors = [
            0 => 'bg-[#2d5a27]', // Admin
            1 => 'bg-[#1e3a8a]', // Manager
            2 => 'bg-[#4b5563]', // Staff
        ];

        // Determine the background: 
        // 1. If we have a roleId, use that color.
        // 2. If no roleId but we have a custom class, let the class handle it.
        // 3. Otherwise, default to the Staff color.
        $bgColor = ($roleId !== null && isset($colors[$roleId])) ? $colors[$roleId] : '';

        // If no custom class provided and no role color, default to Staff
        if (empty($bgColor) && empty($className)) {
            $bgColor = $colors;
        }

        return "
        <span class='{$className} text-white {$bgColor}  px-3 py-1 rounded-full text-[10px] font-medium uppercase tracking-wider'>
            " . htmlspecialchars($label) . "
        </span>
    ";

    }
}