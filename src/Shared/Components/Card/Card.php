<?php
namespace App\Shared\Components;

class Card {
    public static function open() {
        return "<div class='nt-card'>";
    }

    public static function header($title, $subtitle = '') {
        $sub = $subtitle ? "<p class='nt-card-subtitle'>{$subtitle}</p>" : "";
        return "
            <div class='nt-card-header'>
                <h3 class='nt-card-title'>{$title}</h3>
                {$sub}
            </div>
            <div class='nt-card-divider'></div>
        ";
    }

    public static function bodyOpen() {
        return "<div class='nt-card-body'>";
    }

    public static function bodyClose() {
        return "</div>";
    }

    public static function footer($content) {
        return "
            <div class='nt-card-divider'></div>
            <div class='nt-card-footer'>
                {$content}
            </div>
        ";
    }

    public static function close() {
        return "</div>";
    }
}