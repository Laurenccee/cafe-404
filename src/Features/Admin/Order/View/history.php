<?php
use App\Shared\Components\Sidebar\Sidebar;
use App\Shared\Components\InputField\InputField;

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
                <p class="text-lg opacity-70 font-serif text-subtitle">
                    A complete archival record of every artisanal pour.
                </p>
            </div>

            <div class="flex gap-4 items-center">
                <?= InputField::render([
                    'type' => 'text',
                    'id' => 'search',
                    'value' => $_GET['search'] ?? '',
                    'placeholder' => 'Search by Ref ID',
                    'leading' => 'search',
                    'name' => 'search',
                ]) ?>

                <div class="flex gap-0 items-center bg-white border-2 border-[#6f4e37]/5 p-2 rounded-md">
                    <div class="flex flex-col px-3 border-r border-[#6f4e37]/10">
                        <span class="text-xs uppercase text-subtitle tracking-widest opacity-40 font-bold">From</span>
                        <input type="date" id="date-from" name="from" value="<?= $_GET['from'] ?? '' ?>"
                            class="bg-transparent border-none text-xs font-sans p-0 focus:ring-0 text-subtitle">
                    </div>
                    <div class="flex flex-col px-3">
                        <span class="text-xs uppercase text-subtitle tracking-widest opacity-40 font-bold">To</span>
                        <input type="date" id="date-to" name="to" value="<?= $_GET['to'] ?? '' ?>"
                            class="bg-transparent border-none text-xs font-sans p-0 focus:ring-0 text-subtitle">
                    </div>
                </div>
            </div>
        </header>

        <div id="stats-container" class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <section class="bg-white rounded-md p-8 flex flex-col gap-4 border border-[#6f4e37]/5">
                <h3 class="font-bold text-lg uppercase opacity-40 tracking-[0.2em]">Total Records</h3>
                <div class="flex flex-col gap-1">
                    <p class="serif-display text-subtitle opacity-60 italic">Historical Transactions</p>
                    <p class="serif-display text-title font-semibold text-4xl text-primary"><?= count($orders) ?></p>
                </div>
            </section>

            <section class="bg-white rounded-md p-8 flex flex-col gap-4 border border-[#6f4e37]/5">
                <h3 class="font-bold text-lg uppercase opacity-40 tracking-[0.2em]">Average Ticket</h3>
                <div class="flex flex-col gap-1">
                    <p class="serif-display text-subtitle opacity-60 italic">Per Transaction</p>
                    <p class="serif-display text-title font-semibold text-4xl text-primary">
                        ₱<?= number_format($avgOrder ?? 0, 2) ?></p>
                </div>
            </section>

            <section class="bg-white rounded-md p-8 flex flex-col gap-4 border border-[#6f4e37]/5">
                <h3 class="font-bold text-lg uppercase opacity-40 tracking-[0.2em]">Total Gross</h3>
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
            </div>

            <div class="bg-white rounded-xl overflow-hidden border border-[#6f4e37]/5">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#F0EDED] text-[10px] uppercase tracking-[0.2em] font-bold text-secondary/70">
                        <tr>
                            <th class="px-8 py-5">Ref ID</th>
                            <th class="px-8 py-5">Timestamp</th>
                            <th class="px-8 py-5">Items Summary</th>
                            <th class="px-8 py-5 text-right">Total</th>
                            <th class="px-8 py-5 text-right">Paid</th>
                            <th class="px-8 py-5 text-right">Change</th>
                        </tr>
                    </thead>
                    <tbody id="order-ledger-body" class="divide-y divide-[#6f4e37]/10">
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-surface-milk/30 transition-colors group">
                                <td class="px-8 py-6 text-xs font-bold text-subtitle tracking-tight">
                                    #<?= htmlspecialchars($order['order_number']) ?>
                                </td>

                                <td class="px-8 py-6">
                                    <p class="text-sm font-semibold text-title leading-none mb-1">
                                        <?= (new DateTime($order['created_at']))->format('M d, Y') ?>
                                    </p>
                                    <p class="text-xs opacity-50 text-subtitle uppercase tracking-widest">
                                        <?= (new DateTime($order['created_at']))->format('h:i A') ?>
                                    </p>
                                </td>

                                <td class="px-8 py-6 max-w-[350px]">
                                    <p class="text-sm text-subtitle italic line-clamp-2 truncate leading-relaxed">
                                        <?= htmlspecialchars($order['item_summary'] ?? 'No items recorded') ?>
                                    </p>
                                </td>

                                <td class="px-8 py-6 text-right font-semibold text-lg text-title">
                                    ₱<?= number_format($order['total_amount'], 2) ?>
                                </td>

                                <td class="px-8 py-6 text-right font-semibold text-lg text-title">
                                    ₱<?= number_format($order['amount_paid'] ?? 0, 2) ?>
                                </td>

                                <td class="px-8 py-6 text-right font-semibold text-title">
                                    ₱<?= number_format(($order['amount_paid'] ?? 0) - $order['total_amount'], 2) ?>
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.querySelector('input[name="search"]');
        const fromInput = document.getElementById('date-from');
        const toInput = document.getElementById('date-to');

        const tableBody = document.getElementById('order-ledger-body');
        const statsContainer = document.getElementById('stats-container');

        async function updateLedger() {
            const params = new URLSearchParams();

            if (searchInput?.value) params.set('search', searchInput.value.trim());
            if (fromInput?.value) params.set('from', fromInput.value);
            if (toInput?.value) params.set('to', toInput.value);

            const targetUrl = `${window.location.pathname}?${params.toString()}`;

            try {
                if (tableBody) tableBody.style.opacity = '0.5';

                const response = await fetch(targetUrl);
                const html = await response.text();

                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newTableBody = doc.getElementById('order-ledger-body');
                const newStats = doc.getElementById('stats-container');

                if (newTableBody && tableBody) {
                    tableBody.innerHTML = newTableBody.innerHTML;
                }

                if (newStats && statsContainer) {
                    statsContainer.innerHTML = newStats.innerHTML;
                }

                window.history.pushState({}, '', targetUrl);

            } catch (error) {
                console.error('Ledger AJAX Error:', error);
            } finally {
                if (tableBody) tableBody.style.opacity = '1';
            }
        }

        let timer;
        searchInput?.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(updateLedger, 300);
        });

        fromInput?.addEventListener('change', updateLedger);
        toInput?.addEventListener('change', updateLedger);
    });
</script>