<?php

namespace App\Shared\Components;

class Badge
{
    public static function render($label, $roleOrProps = 2, $props = [])
    {

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

        $bgColor = ($roleId !== null && isset($colors[$roleId])) ? $colors[$roleId] : '';

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