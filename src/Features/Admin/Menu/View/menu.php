<?php
use App\Shared\Components\Sidebar\Sidebar;
use App\Shared\Components\Button\Button;
use App\Shared\Components\ComboBox\ComboBoxField;
use App\Shared\Components\InputField\InputField;

$pageTitle = 'Menu Management';
$now = new DateTimeImmutable();
ob_start();
?>

<?php
$categories = $categories ?? [];

$menuItems = $menuItems ?? [];
$inStockCount = $inStockCount ?? 0;
$totalCount = $totalCount ?? 0;
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

<div class="flex  h-screen overflow-hidden bg-background">
    <?= Sidebar::render($adminInfo['username']) ?>

    <main class="flex-1 flex overflow-y-auto smooth-scroll flex-col gap-12 py-14 px-16">
        <header class="flex justify-between items-end">
            <div class="max-w-full">
                <h1 class="serif-display text-title text-7xl">Menu Management</h1>
                <p class="opacity-70 font-serif text-subtitle">
                    Manage your offerings, prices, and availability.
                </p>
            </div>
            <div>
                <?= Button::render("Add New Item", [
                    "leading" => "plus",
                    "href" => "/cafe_404/menu/add"
                ]) ?>
            </div>
        </header>

        <div class="flex flex-col gap-6">
            <section class="grid grid-cols-6 gap-6">
                <div class="p-6 col-span-1 bg-[#F0EDED] rounded-xl">
                    <span class="text-sm opacity-60 font-bold uppercase tracking-widest">Total Items</span>
                    <p class="text-4xl text-title font-semibold mt-2">
                        <?= $totalCount ?>
                    </p>
                </div>
                <div class="p-6 col-span-1 bg-[#e4e4cc] rounded-xl">
                    <span class="text-sm opacity-60 font-bold uppercase tracking-widest">In Stock</span>
                    <p class="text-4xl text-title font-semibold mt-2">
                        <?= $inStockCount ?>
                    </p>
                </div>
                <div class="p-6 col-span-4 bg-[#F0EDED] rounded-xl">
                    <div class="flex gap-4">
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
                            "value" => $_GET['availability'] ?? '',
                            "placeholder" => "Filter by Availability",
                            "disabled" => true,
                            "name" => "availability"
                        ]) ?>
                    </div>
                </div>
            </section>

            <section class="col-span-4 flex flex-col gap-8">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-6">
                    <?php foreach ($menuItems as $item): ?>
                        <div
                            class="group bg-[#F0EDED] rounded-md p-4 border border-transparent hover:border-[#e4e4cc] transition-all duration-300 flex flex-col gap-4">

                            <div class="relative aspect-square rounded-2xl overflow-hidden bg-gray-100">
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
                                <div class="flex justify-between items-start">
                                    <span
                                        class="text-xs text-subtitle uppercase tracking-tighter opacity-60 font-bold"><?= $item['category_name'] ?></span>
                                    <span class="text-[10px] opacity-80 font-mono"><?= $item['product_code'] ?></span>
                                </div>
                                <h3 class="serif-display text-xl text-title truncate"><?= $item['name'] ?></h3>
                                <p class="text-xs text-subtitle opacity-80 capitalize truncate h-8 leading-relaxed">
                                    <?= $item['description'] ?>
                                </p>
                            </div>

                            <div class="mt-auto pt-4 flex items-center justify-between border-t border-gray-50 px-1">
                                <span class="text-lg text-title">₱<?= number_format($item['price'], 2) ?></span>

                                <div class="flex gap-1">
                                    <a href="/cafe_404/menu/edit?id=<?= $item['id'] ?>"
                                        class="p-2 bg-white/30 hover:bg-[#e4e4cc] rounded-sm transition-colors text-secondary"
                                        title="Edit">
                                        <i data-lucide="edit-2" class="size-4"></i>
                                    </a>
                                    <a href="/cafe_404/menu/delete?id=<?= $item['id'] ?>"
                                        class="p-2 bg-white/30 hover:bg-rose-200 rounded-sm transition-colors text-rose-400 hover:text-rose-600"
                                        title="Delete"
                                        onclick="return confirm('Are you sure you want to delete this item?');">
                                        <i data-lucide="trash-2" class="size-4"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <p class="text-xs opacity-50 italic">Showing page <?= $currentPage ?> of <?= $totalPages ?></p>
                    <div class="flex gap-2">
                        <?php if ($currentPage > 1): ?>
                            <a href="?page=<?= $currentPage - 1 ?>"
                                class="px-4 py-2 bg-white rounded-full border border-gray-200 text-xs hover:bg-[#e4e4cc] transition-all">Previous</a>
                        <?php endif; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?page=<?= $currentPage + 1 ?>"
                                class="px-4 py-2 bg-[#e4e4cc] rounded-full text-xs font-bold hover:shadow-md transition-all">Next
                                Page</a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

        </div>
    </main>
</div>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . 'src/Shared/Layouts/layout.php';
?>