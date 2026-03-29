<?php
use App\Shared\Components\Sidebar\Sidebar;
use App\Shared\Components\Button\Button;

$pageTitle = 'The Editorial Barista | Management';
$now = new DateTimeImmutable();
ob_start();
?>

<div class="flex  h-screen overflow-hidden bg-background">
    <?= Sidebar::render($adminInfo['username']) ?>

    <main class="flex-1 flex overflow-y-auto smooth-scroll flex-col gap-12 py-14 px-16">
        <header class="flex justify-between items-end">
            <div class="max-w-full">
                <h1 class="serif-display text-title text-7xl">Dashboard</h1>
                <p class="text-lg opacity-70 font-serif text-subtitle ">
                    Curating the daily flow of artisanal coffee and community.
                </p>
            </div>
            <div class="max-w-xl text-right">
                <h1 class="serif-display text-subtitle font-bold text-lg">Current Session</h1>
                <p class="text-lg opacity-70 font-serif text-subtitle">
                    <?= $now->format('l, F d, Y') ?>
                </p>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">

            <div class="lg:col-span-3 grid grid-cols-2 gap-10">
                <section class="bg-white rounded-xl p-8 flex flex-col gap-6 ">
                    <h3 class=" font-bold text-lg uppercase opacity-40 tracking-[0.2em]">Today's Income</h3>
                    <div class="flex flex-col gap-1">
                        <p class="serif-display text-subtitle opacity-60">23 Transactions</p>
                        <p class="serif-display text-title font-semibold text-4xl text-primary">₱8,560.00</p>
                    </div>
                </section>

                <section class="bg-white rounded-xl p-8 flex flex-col gap-6">
                    <h3 class=" font-bold text-lg uppercase opacity-40 tracking-[0.2em]">Monthly Income</h3>
                    <div class="flex flex-col gap-1">
                        <p class="serif-display text-subtitle opacity-60">435 Transactions</p>
                        <p class="serif-display text-title font-semibold text-4xl text-primary">₱120,450.00</p>
                    </div>
                </section>

                <section
                    class="col-span-2 bg-[#2d5a27] rounded-xl p-8 flex flex-col gap-10 relative overflow-hidden group">
                    <div class="flex flex-col gap-2 relative z-10">
                        <h3 class="font-bold text-lg text-white uppercase opacity-80 tracking-[0.2em]">
                            Performance Outlook
                        </h3>
                        <p class="text-emerald-200/60 italic font-serif">
                            Peak brewing hours recorded between 8:00 AM and 10:30 AM today.
                        </p>
                    </div>

                    <div class="flex items-end justify-between gap-64 h-full relative z-10">

                        <div class="flex flex-1 items-end gap-2 h-full">
                            <div class="w-full bg-white/10 rounded-t-sm h-[30%] transition-all group-hover:h-[40%]">
                            </div>
                            <div class="w-full bg-white/20 rounded-t-sm h-[50%]"></div>
                            <div class="w-full bg-emerald-200 rounded-t-sm h-[90%] ">
                            </div>
                            <div class="w-full bg-white/10 rounded-t-sm h-[20%] transition-all group-hover:h-[30%]">
                            </div>
                            <div class="w-full bg-white/20 rounded-t-sm h-[45%]"></div>
                            <div class="w-full bg-white/5 rounded-t-sm h-[15%]"></div>
                        </div>

                        <div class="flex flex-col">
                            <p class="text-emerald-200 text-xs font-bold uppercase tracking-widest opacity-60">Current
                                Trend</p>
                            <p class="serif-display text-white font-semibold text-4xl">+12%</p>
                        </div>

                    </div>

                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-emerald-500/10 blur-[80px]"></div>
                </section>
            </div>

            <section class="lg:col-span-2 bg-[#e4e4cc] text-[#6f4e37] p-8 rounded-xl flex flex-col gap-10">
                <div class="flex justify-between items-center">
                    <h2 class="serif-display text-title text-4xl italic">Popular Orders</h2>
                    <i data-lucide="trending-up" class="size-6 opacity-70"></i>
                </div>

                <div class="flex flex-col gap-4">
                    <?php
                    $popularOrders = [
                        ['name' => 'Espresso', 'price' => '150.00', 'sold' => 120],
                        ['name' => 'Americano', 'price' => '155.00', 'sold' => 95],
                        ['name' => 'Cortado', 'price' => '170.00', 'sold' => 80],
                        ['name' => 'Flat White', 'price' => '185.00', 'sold' => 65],

                    ];

                    foreach ($popularOrders as $index => $order): ?>
                        <div
                            class="flex gap-4 justify-center items-center group cursor-pointer hover:bg-surface-milk p-2 rounded-lg transition-colors">
                            <div class="h-12 w-12 flex items-center justify-center">
                                <span class="serif-display text-primary font-bold text-sm">0<?= $index + 1 ?></span>
                            </div>
                            <div class="flex-1/2">
                                <p class="text-lg font-bold uppercase tracking-[0.2em] opacity-60">
                                    <?= $order['name'] ?>
                                </p>
                                <p class="serif-display font-semibold text-xs text-primary">₱
                                    <?= $order['price'] ?>
                                </p>
                            </div>
                            <div class="flex-1">
                                <p class="serif-display font-bold text-lg text-primary">
                                    <?= $order['sold'] ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
                <div>
                    <?= Button::render("View All", [
                        "href" => "/cafe_404/orders",
                        "trailing" => 'chevron-right',
                        'variant' => 'tertiary',
                    ]); ?>
                </div>

            </section>
        </div>

        <section class="flex flex-col gap-8">
            <div class="flex justify-between items-baseline">
                <div>
                    <h2 class="serif-display text-title text-4xl">Recent Transaction History</h2>
                    <span>Real-time update of curated orders from the counter.</span>
                </div>
                <div class="flex gap-4">
                    <?= Button::render("Export CSV", [
                        "href" => "#",
                        "leading" => 'download',
                        'variant' => 'secondary',
                    ]); ?>
                    <?= Button::render("View All", [
                        "href" => "/cafe_404/orders",
                        "trailing" => 'chevron-right',
                        'variant' => 'primary',
                    ]); ?>
                </div>
            </div>

            <div class="bg-white rounded-xl overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-[#F0EDED] text-xs uppercase tracking-[0.2em] font-bold text-secondary">
                        <tr>
                            <th class="px-8 py-5">Ref ID</th>
                            <th class="px-8 py-5">Timestamp</th>
                            <th class="px-8 py-5">Order Detail</th>
                            <th class="px-8 py-5 text-right">Method</th>
                            <th class="px-8 py-5 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#6f4e37]/20">
                        <tr class="hover:bg-surface-milk/50 transition-colors">
                            <td class="px-8 py-6  text-xs">#REF-404-001</td>
                            <td class="px-8 py-6 text-sm opacity-70">14:02 PM</td>
                            <td class="px-8 py-6 text-sm">Ethiopian Yirgacheffe (V60)</td>
                            <td class="px-8 py-6 text-right text-sm uppercase tracking-widest">G-Cash</td>
                            <td class="px-8 py-6 text-right text-sm text-title">₱180.00</td>
                        </tr>
                        <tr class="hover:bg-slate-100 transition-colors">
                            <td class="px-8 py-6  text-xs">#REF-404-001</td>
                            <td class="px-8 py-6 text-sm opacity-70">14:02 PM</td>
                            <td class="px-8 py-6 text-sm">Ethiopian Yirgacheffe (V60)</td>
                            <td class="px-8 py-6 text-right text-sm uppercase tracking-widest">G-Cash</td>
                            <td class="px-8 py-6 text-right text-sm text-title">₱180.00</td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . 'src/Shared/Layouts/layout.php';
?>