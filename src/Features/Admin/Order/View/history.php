<?php
use App\Shared\Components\Sidebar\Sidebar;
use App\Shared\Components\Button\Button;

$pageTitle = 'The Editorial Ledger | History';
$now = new DateTimeImmutable();
ob_start();
?>

<div class="flex h-screen overflow-hidden bg-background">
    <?= Sidebar::render($userInfo['username']) ?>

    <main class="flex-1 flex overflow-y-auto smooth-scroll flex-col gap-12 py-14 px-16">
        <header class="flex justify-between items-end">
            <div class="max-w-full">
                <h1 class="serif-display text-title text-7xl">Order History</h1>
                <p class="text-lg opacity-70 font-serif text-subtitle ">
                    A complete archival record of every artisanal pour.
                </p>
            </div>

            <div class="flex gap-4 items-center bg-white border border-[#6f4e37]/5 p-2 rounded-xl">
                <div class="flex flex-col px-3 border-r border-[#6f4e37]/10">
                    <span class="text-[9px] uppercase tracking-widest opacity-40 font-bold">From</span>
                    <input type="date" class="bg-transparent border-none text-xs font-sans p-0 focus:ring-0 text-title">
                </div>
                <div class="flex flex-col px-3">
                    <span class="text-[9px] uppercase tracking-widest opacity-40 font-bold">To</span>
                    <input type="date" class="bg-transparent border-none text-xs font-sans p-0 focus:ring-0 text-title">
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <section class="bg-white rounded-xl p-8 flex flex-col gap-4 border border-[#6f4e37]/5">
                <h3 class="font-bold text-[10px] uppercase opacity-40 tracking-[0.2em]">Total Records</h3>
                <div class="flex flex-col gap-1">
                    <p class="serif-display text-subtitle opacity-60 italic">Historical Transactions</p>
                    <p class="serif-display text-title font-semibold text-4xl text-primary"><?= count($orders) ?></p>
                </div>
            </section>

            <section class="bg-white rounded-xl p-8 flex flex-col gap-4 border border-[#6f4e37]/5">
                <h3 class="font-bold text-[10px] uppercase opacity-40 tracking-[0.2em]">Average Ticket</h3>
                <div class="flex flex-col gap-1">
                    <p class="serif-display text-subtitle opacity-60 italic">Per Transaction</p>
                    <p class="serif-display text-title font-semibold text-4xl text-primary">
                        ₱<?= number_format($avgOrder ?? 0, 2) ?></p>
                </div>
            </section>

            <section class="bg-white rounded-xl p-8 flex flex-col gap-4 border border-[#6f4e37]/5">
                <h3 class="font-bold text-[10px] uppercase opacity-40 tracking-[0.2em]">Total Gross</h3>
                <div class="flex flex-col gap-1">
                    <p class="serif-display text-subtitle opacity-60 italic">All Time Revenue</p>
                    <p class="serif-display text-title font-semibold text-4xl text-primary">
                        ₱<?= number_format($lifetimeTotal ?? 0, 2) ?></p>
                </div>
            </section>
        </div>

        <section class="flex flex-col gap-8 mb-10">
            <div class="flex justify-between items-baseline">
                <div>
                    <h2 class="serif-display text-title text-4xl">Historical Ledger</h2>
                    <span class="opacity-60 font-serif">Curated selection of all past orders.</span>
                </div>
                <div class="flex gap-4">
                    <?= Button::render("Export CSV", [
                        "href" => "#",
                        "leading" => 'download',
                        'variant' => 'secondary',
                    ]); ?>
                </div>
            </div>

            <div class="bg-white rounded-xl overflow-hidden border border-[#6f4e37]/5">
                <table class="w-full text-left">
                    <thead class="bg-[#F0EDED] text-xs uppercase tracking-[0.2em] font-bold text-secondary">
                        <tr>
                            <th class="px-8 py-5">Ref ID</th>

                            <th class="px-8 py-5">Timestamp</th>
                            <th class="px-8 py-5 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#6f4e37]/20">
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-surface-milk/50 transition-colors">
                                <td class="px-8 py-6 text-xs font-bold text-primary tracking-tight">
                                    #<?= htmlspecialchars($order['order_number']) ?>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-sm font-semibold text-title leading-none mb-1">
                                        <?= (new DateTime($order['created_at']))->format('M d, Y') ?>
                                    </p>
                                    <p class="text-[10px] opacity-50 uppercase tracking-widest font-sans">
                                        <?= (new DateTime($order['created_at']))->format('h:i A') ?>
                                    </p>
                                </td>
                                <td class="px-8 py-6 text-right font-serif font-bold text-lg text-title">
                                    ₱<?= number_format($order['total_amount'], 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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