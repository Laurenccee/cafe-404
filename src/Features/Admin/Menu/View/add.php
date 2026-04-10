<?php
use App\Shared\Components\Sidebar\Sidebar;
use App\Shared\Components\Button\Button;
use App\Shared\Components\ComboBox\ComboBoxField;
use App\Shared\Components\InputField\InputField;
use App\Shared\Components\TextArea;
use App\Shared\Components\FileDrop;

$pageTitle = 'Menu Management';
$now = new DateTimeImmutable();
ob_start();
?>

<?php
$categories = $categories ?? [];
$menuItems = $menuItems ?? [];
$adminInfo = $adminInfo ?? ['username' => 'Guest'];
$form = $props['form'] ?? '';
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
        </header>
        <form action="/cafe_404/menu/store" method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 lg:grid-cols-7 gap-10">
                <section class="lg:col-span-3 rounded-2xl overflow-hidden">
                    <?= FileDrop::render('product_image', "", "", [
                        "id" => "product_image"
                    ]) ?>
                </section>
                <section class="bg-white col-span-4 p-6 rounded-2xl overflow-hidden">
                    <div class="flex flex-col items-between justify-between h-full">
                        <div class="flex flex-col gap-4">

                            <?= InputField::render([
                                "type" => "text",
                                "name" => "product_code",
                                "label" => "Product Code",
                                "leading" => "",
                                "disabled" => true,
                                "placeholder" => "Auto-generated"
                            ]) ?>
                            <?= InputField::render([
                                "type" => "text",
                                "name" => "name",
                                "label" => "Item Name",
                                "leading" => "",
                                "placeholder" => "Enter item name"
                            ]) ?>

                            <?= TextArea::render([
                                "label" => "Description",
                                "name" => "description",
                                "placeholder" => "Enter item description",
                                "rows" => 5,
                            ]) ?>
                            <div class="flex gap-2">
                                <?= ComboBoxField::render([
                                    "name" => "category_id",
                                    "options" =>
                                        array_map(fn($cat) => [
                                            "value" => $cat['id'],
                                            "label" => $cat['category_name']
                                        ], $categories),

                                    "label" => "Category",
                                    "leading" => "",
                                    "placeholder" => "Select Category",
                                ]) ?>
                                <?= ComboBoxField::render([
                                    "name" => "is_available",
                                    "options" => (
                                        array_map(fn($item) => [
                                            "value" => $item['id'],
                                            "label" => $item['label']
                                        ], $availability)
                                    ),

                                    "label" => "Availability",
                                    "leading" => "",
                                    "placeholder" => "Select Availability",
                                ]) ?>
                                <?= InputField::render([
                                    "name" => "price",
                                    "type" => "text",
                                    "label" => "Price",
                                    "leading" => "",
                                    "placeholder" => "Price in PHP"
                                ]) ?>
                            </div>

                        </div>
                        <div>
                            <?= Button::render("Add Item", [

                                "type" => "submit",
                                "leading" => 'plus',
                                'variant' => 'tertiary',
                            ]); ?>
                        </div>
                    </div>
                </section>
            </div>
        </form>
    </main>
</div>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . 'src/Shared/Layouts/layout.php';
?>

<script src="/cafe_404/public/assets/js/image.js"></script>