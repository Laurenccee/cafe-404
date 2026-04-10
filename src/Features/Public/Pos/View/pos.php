<?php
use App\Shared\Components\Sidebar\Sidebar;
use App\Shared\Components\Button\Button;
use App\Shared\Components\InputField\InputField;
use App\Shared\Components\ComboBox\ComboBoxField;


$pageTitle = 'POS Interface | Cafe 404';
ob_start();

$userRole = (int) ($_SESSION['role_id'] ?? -1);
$username = $_SESSION['username'] ?? 'Staff';

$categories = $categories ?? [];

$menuItems = $menuItems ?? [];
$inStockCount = $inStockCount ?? 0;
$adminInfo = $adminInfo ?? ['username' => 'Guest'];

$availabilityMap = [];
foreach ($availability as $status) {
    $colorClass = 'bg-gray-400';
    
    $label = strtolower($status['label']);
    if (str_contains($label, 'available') && !str_contains($label, 'not')) {
        $colorClass = 'bg-emerald-500';
    } elseif (str_contains($label, 'not available')) {
        $colorClass = 'bg-rose-500';
    } elseif (str_contains($label, 'sold out')) {
        $colorClass = 'bg-amber-500';
    }

    $availabilityMap[$status['id']] = [
        'label' => $status['label'],
        'class' => $colorClass
    ];
}
?>

<div class="flex h-screen w-full overflow-hidden bg-surface text-on-surface">
    <?php if ($userRole === 0 || $userRole === 1): ?>
        <?= Sidebar::render($username) ?>
    <?php endif; ?>

    <main class="flex-1 flex flex-col overflow-hidden <?= ($userRole === 0 || $userRole === 1) ? '' : 'w-full' ?>">

        <?php if ($userRole !== 0 && $userRole !== 1): ?>
            <nav class="w-full bg-white border-b-2 border-[#6f4e37]/10 px-12 py-2 flex justify-between items-center z-20">
                <div class="flex items-center gap-4">
                    <span class="font-serif text-2xl text-title tracking-tighter italic">Cafe 404</span>
                    <span class="h-4 w-[1px] bg-[#6f4e37]/20"></span>
                    <span class="text-xs uppercase tracking-[0.2em] font-bold text-subtitle opacity-60">POS
                        Interface</span>
                </div>

                <div class="flex items-center gap-6">
                    <?= Button::render("Logout", [
                        "variant" => "tertiary",
                        "href" => "/cafe_404/logout",
                        "trailing" => "log-out"
                    ]) ?>
                </div>
            </nav>
        <?php endif; ?>

        <div class="flex-1 flex overflow-hidden">

            <section class="flex-1 flex flex-col gap-10 p-12 overflow-y-auto smooth-scroll">
                <header class="flex justify-between items-end">
                    <div>
                        <h1 class="font-serif text-7xl text-title leading-none">Ordering Menu</h1>
                        <p class="font-serif text-secondary opacity-70 text-lg">The cafe ordering system</p>
                    </div>
                </header>
                <div class="p-6 col-span-4 bg-[#F0EDED] rounded-xl">
                    <div class="gap-4 flex">
                        <?= InputField::render([
                            "label" => "Search",
                            "type" => "text",
                            "trailing" => "search",
                            "value" => $_GET['search'] ?? '',
                            "disabled" => false,
                            "placeholder" => "Search Item...",
                            "name" => "search"

                        ]) ?>
                        <?= ComboBoxField::render([
                            "label" => "Category",
                            "options" => array_merge(
                                [
                                    [
                                        "value" => "",
                                        "label" => "None / All Categories"
                                    ]
                                ],
                                array_map(fn($cat) => [
                                    "value" => $cat['id'],
                                    "label" => $cat['category_name']
                                ], $categories),
                            ),
                            "value" => $_GET['category'] ?? '',
                            "placeholder" => "Filter by Category",
                            "disabled" => true,
                            "name" => "category"
                        ]) ?>
                        <?= ComboBoxField::render([
                            "label" => "Availability",
                            "options" => array_merge(
                                [
                                    [
                                        "value" => "",
                                        "label" => "All"
                                    ]
                                ],
                                array_map(fn($item) => [
                                    "value" => $item['id'],
                                    "label" => $item['label']
                                ], $availability),

                            ),
                            "value" => $_GET['available'] ?? '',
                            "placeholder" => "Filter by Availability",
                            "disabled" => true,
                            "name" => "availability"
                        ]) ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php foreach ($menuItems as $item): ?>
                        <div class="menu-card bg-[#F0EDED] rounded-md p-4 border-2 border-[#6f4e37]/5 flex flex-col gap-4"
                            data-category="<?= strtolower($item['category_name'] ?? 'uncategorized') ?>">

                            <div class="group relative aspect-square rounded-2xl overflow-hidden bg-gray-100">
                                <img src="/cafe_404/public/assets/images/uploads/menu/<?= htmlspecialchars($item['image_path']) ?>"
                                    alt="<?= htmlspecialchars($item['name']) ?>"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                    style="object-position: <?= $item['pos_x'] ?>% <?= $item['pos_y'] ?>%;">
                                <div class="absolute top-3 left-3">
                                    <?php
                                    $status = $availabilityMap[$item['is_available']] ?? [
                                        'label' => 'Unknown', 
                                        'class' => 'bg-gray-400'
                                    ];
                                    ?>
                                    <span class="px-3 py-1 text-white text-[10px] font-bold uppercase rounded-full backdrop-blur-md <?= $status['class'] ?>">
                                        <?= htmlspecialchars($status['label']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-1 px-1">
                                <span class="text-xs text-subtitle uppercase tracking-tighter opacity-60 font-bold">
                                    <?= $item['category_name'] ?>
                                </span>
                                <div class="flex items-center justify-between">
                                    <h3 class="serif-display text-xl text-title truncate"><?= $item['name'] ?></h3>
                                    <span class="font-bold uppercase tracking-tighter text-subtitle">
                                        ₱<?= number_format($item['price'], 2) ?>
                                    </span>
                                </div>
                                <p class="text-xs text-subtitle truncate opacity-80 h-8 leading-relaxed">
                                    <?= $item['description'] ?>
                                </p>
                            </div>
                            <div class="flex flex-col gap-3">
                            <?php 
                                // Get the specific status label for this item
                                $currentStatus = strtolower($availabilityMap[$item['is_available']]['label'] ?? 'available');
                                
                                echo Button::render("Add Item", [
                                    "leading"      => "plus",
                                    "variant"      => "tertiary",
                                    "availability" => $currentStatus, // Pass the status here
                                    "onclick"      => "addToOrder(" . htmlspecialchars(json_encode([
                                        'id' => $item['id'],
                                        'name' => $item['name'],
                                        'price' => $item['price'],
                                        'image_path' => $item['image_path']
                                    ])) . ")"
                                ]); 
                            ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <aside class="min-w-[24rem] border-l-2 border-[#6f4e37]/10 bg-[#f5f1f0] py-10 px-5 flex flex-col z-10">
                <h2 class="font-sans-serif text-title mb-8 text-3xl tracking-tight">Current Order</h2>

                <div id="order-items-container" class="flex-1 space-y-2 overflow-y-auto pr-2 mb-8">
                    <div class="flex flex-col gap-2 items-center justify-center h-full text-secondary ">
                        <i data-lucide="shopping-cart" class="size-10 opacity-40"></i>
                        <p class="text-subtitle font-semibold opacity-60">No items in order</p>
                    </div>
                </div>

                <div class="mt-auto pt-4 border-t border-[#6f4e37]/20">
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between items-center font-semibold text-2xl text-title">
                            <span class="font-sans-serif">Total</span>
                            <div>
                                <span class="text-3xl">₱</span>
                                <span id="total-display" class="font-sans-serif text-2xl">0.00</span>
                            </div>
                        </div>
                    </div>

                    <div id="cash-input-section" class="flex flex-col gap-2 mb-6">
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
            </aside>
        </div>
    </main>
</div>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . 'src/Shared/Layouts/layout.php';
?>

<script src="/cafe_404/public/assets/js/addItem.js"></script>