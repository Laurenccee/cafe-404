<?php
use App\Shared\Components\Sidebar\Sidebar;
use App\Shared\Components\Badge;
use App\Shared\Components\Button\Button;
use App\Shared\Components\InputField\InputField;

$pageTitle = 'POS Interface | Cafe 404';
ob_start();
?>

<div class="flex h-screen w-full overflow-hidden bg-surface text-on-surface">
    <?php
    $userRole = (int) ($_SESSION['role_id'] ?? -1);
    if ($userRole === 0 || $userRole === 1): ?>
        <?= Sidebar::render($userInfo['username']) ?>
    <?php endif; ?>



    <main class="flex-1 flex overflow-hidden <?= ($userRole === 0 || $userRole === 1) ? '' : 'w-full' ?>">

        <section class="flex-1 flex flex-col gap-10 p-12 overflow-y-auto smooth-scroll">
            <header class="flex justify-between items-end ">
                <div>
                    <h1 class="font-serif text-7xl text-title leading-none">Ordering Menu</h1>
                    <p class="font-serif text-secondary opacity-70 text-lg">The cafe ordering system</p>
                </div>

            </header>
            <nav class="flex gap-2.5 overflow-x-auto pb-2">
                <div class="min-w-fit">
                    <?= Button::render("All", [
                        "variant" => "primary",
                        "size" => "sm",
                        "onclick" => "filterMenu('all', this)"
                    ]) ?>
                </div>

                <?php foreach ($categories as $category): ?>
                    <div class="min-w-fit">
                        <?= Button::render($category['category_name'], [
                            "variant" => "secondary",
                            "size" => "sm",
                            "onclick" => "filterMenu('" . strtolower($category['category_name']) . "', this)"
                        ]) ?>
                    </div>
                <?php endforeach; ?>
            </nav>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($menuItems as $item): ?>
                    <div class="menu-card bg-[#F0EDED] rounded-md p-4 border-2 border-[#6f4e37]/5 flex flex-col gap-4"
                        data-category="<?= strtolower($item['category_name'] ?? 'uncategorized') ?>">
                        <div class=" group relative aspect-square rounded-2xl overflow-hidden bg-gray-100">
                            <img src="/cafe_404/public/assets/images/uploads/menu/<?= htmlspecialchars($item['image_path']) ?>"
                                alt="<?= htmlspecialchars($item['name']) ?>"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                style="object-position: <?= $item['pos_x'] ?>% <?= $item['pos_y'] ?>%;">
                            <div class="absolute top-3 left-3">
                                <?= $item['is_available']
                                    ? '<span class="px-3 py-1 bg-emerald-500/90 opacity-80 hover:opacity-100 text-white text-[10px] font-bold uppercase tracking-widest rounded-full backdrop-blur-md">In Stock</span>'
                                    : '<span class="px-3 py-1 bg-rose-500/90 opacity-80 hover:opacity-100 text-white text-[10px] font-bold uppercase tracking-widest rounded-full backdrop-blur-md">Sold Out</span>'
                                    ?>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1 px-1">
                            <div class="flex justify-between items-start">
                                <span class="text-xs text-subtitle uppercase tracking-tighter opacity-60 font-bold">
                                    <?= $item['category_name'] ?>
                                </span>

                            </div>
                            <div class="flex items-center justify-between">
                                <h3 class="serif-display text-xl text-title truncate">
                                    <?= $item['name'] ?>
                                </h3>
                                <span class="font-bold uppercase tracking-tighter text-subtitle">₱
                                    <?= number_format($item['price'], 2) ?>
                                </span>
                            </div>
                            <p class="text-xs text-subtitle opacity-80 line-clamp-2 truncate h-8 leading-relaxed">
                                <?= $item['description'] ?>
                            </p>
                        </div>

                        <div class=" flex flex-col gap-3">

                            <?= Button::render("Add Item", [
                                "leading" => "plus",
                                "type" => "submit",
                                "variant" => "tertiary",
                                "disabled" => !$item['is_available'],
                                "onclick" => "addToOrder(" . htmlspecialchars(json_encode([
                                    'id' => $item['id'],
                                    'name' => $item['name'],
                                    'price' => $item['price'],
                                    'image_path' => $item['image_path'],

                                ])) . ")"


                            ]) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <aside class="min-w-[24rem] border-l-2 border-[#6f4e37]/10 bg-[#f5f1f0] py-10 px-5 flex flex-col  z-10">
            <h2 class="font-sans-serif text-title mb-8 text-3xl tracking-tight">Current Order</h2>

            <div id="order-items-container" class="flex-1 space-y-2 overflow-y-auto pr-2 mb-8">
                <div class="flex flex-col gap-2 items-center justify-center h-full text-secondary ">
                    <i data-lucide="shopping-cart" class="size-10 opacity-40"></i>
                    <p class="text-subtitle font-semibold opacity-60">No items in order</p>
                </div>
            </div>

            <div class=" mt-auto">
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between items-center font-semibold text-2xl text-title">
                        <span class="font-sans-serif">Total</span>
                        <div>
                            <span class="text-3xl">₱</span>
                            <span id="total-display" class="font-sans-serif text-2xl">0.00</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="space-y-7 pt-4 border-t border-[#6f4e37]/20">
                <div id="cash-input-section" class="flex flex-col gap-2">
                    <div class="flex justify-between items-end">
                        <label class="text-xs uppercase tracking-widest text-subtitle font-bold opacity-70">Amount
                            Received</label>
                        <span id="change-display" class="text-xs font-serif font-bold text-[#6f4e37]">Change:
                            ₱0.00</span>
                    </div>
                    <?= InputField::render([
                        "type" => "number",
                        "id" => "cash-amount",
                        "placeholder" => "0.00",
                        "oninput" => "calculateChange()",
                        "class" => "w-full bg-white border-2 border-[#6f4e37]/10 rounded-md p-3 font-mono text-xl focus:border-primary outline-none transition-all"
                    ]) ?>
                </div>
                <div class=" mt-auto">
                    <div class="space-y-3">
                        <div class="flex justify-between gap-5">
                            <?= Button::render("Clear", [
                                "variant" => "secondary",
                                "class" => "w-full border-2 border-[#6f4e37]/10 bg-[#e4e4cc] text-subtitle hover:bg-[#e4e4cc]/90",
                                "onclick" => "clearOrder()",
                            ]) ?>
                            <?= Button::render("Checkout", [
                                "variant" => "primary",
                                "class" => "w-full",
                                "onclick" => "processCheckout()",
                            ]) ?>
                        </div>
                    </div>
                </div>
        </aside>
    </main>
</div>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . 'src/Shared/Layouts/layout.php';
?>

<script src="/cafe_404/public/assets/js/addItem.js"></script>
<script src="/cafe_404/public/assets/js/itemFilter.js"></script>