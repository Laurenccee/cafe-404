<?php

namespace App\Shared\Components\Sidebar;
use App\Shared\Components\Button\Button;

class Sidebar
{
    public static function render($activeUser = 'Admin')
    {
        // Get the current path (e.g., /cafe_404/menu)
        $currentUri = $_SERVER['REQUEST_URI'];

        ob_start();
        ?>
        <aside class="min-w-[18rem] flex flex-col bg-[#f5f1f0] min-h-screen border-r-2 border-[#6f4e37]/10">
            <div class="serif-display p-10 tracking-tight text-[#002c02]">
                <h1 class="text-2xl font-black text-title">Coffee 404</h1>
                <p class="text-sm text-subtitle italic">Coffee not found!</p>
            </div>

            <nav class="flex flex-col bg-white p-10 gap-6 text-sm uppercase tracking-widest font-bold">
                <?php
                $menuItems = [
                    ['label' => 'Overview', 'path' => '/cafe_404/dashboard'],
                    ['label' => 'Menu Management', 'path' => '/cafe_404/menu'],
                    ['label' => 'User Management', 'path' => '/cafe_404/users'],
                    ['label' => 'Order History', 'path' => '/cafe_404/order/history'],
                ];

                foreach ($menuItems as $item):
                    // Check if current URI starts with the item path
                    $isActive = strpos($currentUri, $item['path']) !== false;

                    // Base classes for every link
                    $classes = "transition-all duration-300 pb-1 w-max border-b-2 ";

                    // Active vs Inactive classes
                    $classes .= $isActive
                        ? "text-[#002c02] border-[#002c02]"
                        : "text-subtitle border-transparent hover:text-[#79573f] hover:border-[#79573f]";
                    ?>
                    <a href="<?= $item['path'] ?>" class="<?= $classes ?>">
                        <?= $item['label'] ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="mt-auto p-10">
                <?= Button::render("Point-Of-Sale", [
                    "href" => "/cafe_404/pos",
                    "trailing" => 'arrow-right',
                    'variant' => 'tertiary',
                ]); ?>
            </div>
            <div class="mt-auto p-10">
                <p class="text-xs text-subtitle uppercase opacity-50 mb-2 tracking-widest">Authenticated as</p>
                <p class="serif-display text-title text-lg capitalize mb-6">
                    <?= htmlspecialchars($activeUser) ?>
                </p>
                <?= Button::render("Logout", [
                    "href" => "/cafe_404/logout",
                    "trailing" => 'arrow-right',
                    'variant' => 'primary',
                ]); ?>
            </div>
        </aside>
        <?php
        return ob_get_clean();
    }
}