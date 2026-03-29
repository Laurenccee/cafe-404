<?php
use App\Shared\Components\Sidebar\Sidebar;
use App\Shared\Components\Button\Button;
use App\Shared\Components\ComboBox\ComboBoxField;
use App\Shared\Components\InputField\InputField;
use App\Shared\Components\TextArea;
use App\Shared\Components\FileDrop;

$pageTitle = 'The Editorial Barista | Management';
$now = new DateTimeImmutable();
ob_start();
?>

<?php
$categories = $categories ?? [];
$menuItems = $menuItems ?? [];
$form = $props['form'] ?? '';
?>
<div class="flex  h-screen overflow-hidden bg-background">
    <?= Sidebar::render($adminInfo['username'] ?? 'Admin') ?>

    <main class="flex-1 flex overflow-y-auto smooth-scroll flex-col gap-12 py-14 px-16">
        <header class="flex justify-between items-end">
            <div class="max-w-full">
                <h1 class="serif-display text-title text-7xl">Menu Management</h1>
                <p class="opacity-70 font-serif text-subtitle">
                    Manage your offerings, prices, and availability.
                </p>
            </div>
        </header>
        <form action="/cafe_404/menu/update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $item['id'] ?>">

            <input type="hidden" name="product_image_pos_x" id="pos-x-product_image" value="<?= $item['pos_x'] ?>">
            <input type="hidden" name="product_image_pos_y" id="pos-y-product_image" value="<?= $item['pos_y'] ?>">

            <div class="grid grid-cols-1 lg:grid-cols-7 gap-10">
                <section class="lg:col-span-3 rounded-2xl overflow-hidden">
                    <?= FileDrop::render('product_image', "/cafe_404/public/assets/images/uploads/menu/" . $item['image_path'], "", [
                        "id" => "product_image",
                        "init_x" => $item['pos_x'],
                        "init_y" => $item['pos_y']
                    ]) ?>
                </section>
                <section class="bg-white col-span-4 p-6 rounded-2xl overflow-hidden">
                    <div class="flex flex-col items-between justify-between h-full">
                        <div class="flex flex-col gap-4">

                            <?= InputField::render([
                                "type" => "text",
                                "name" => "product_code",
                                "label" => "Product Code",
                                "value" => $item['product_code'],
                                "leading" => "",
                                "disabled" => true,
                                "placeholder" => "Auto-generated"
                            ]) ?>
                            <?= InputField::render([
                                "type" => "text",
                                "name" => "name",
                                "label" => "Item Name",
                                "value" => $item['name'],
                                "leading" => "",
                                "placeholder" => "Enter item name"
                            ]) ?>

                            <?= TextArea::render([
                                "label" => "Description",
                                "name" => "description",
                                "value" => $item['description'],
                                "placeholder" => "Enter item description",
                                "rows" => 5,
                            ]) ?>
                            <div class="flex gap-2">
                                <?= ComboBoxField::render([
                                    "name" => "category_id",
                                    "value" => $item['category_id'],
                                    "options" => array_merge(
                                        [
                                            [
                                                "value" => "",
                                                "label" => "Select Category"
                                            ]
                                        ],
                                        array_map(fn($cat) => [
                                            "value" => $cat['id'],
                                            "label" => $cat['category_name']
                                        ], $categories),
                                    ),

                                    "label" => "Category",
                                    "leading" => "",
                                    "placeholder" => "Select Category",
                                ]) ?>
                                <?= InputField::render([
                                    "name" => "price",
                                    "type" => "text",
                                    "label" => "Price",
                                    "leading" => "",
                                    "placeholder" => "Price in PHP",
                                    "value" => $item['price']
                                ]) ?>
                            </div>

                        </div>
                        <div>
                            <?= Button::render("Update Item", [

                                "type" => "submit",
                                "leading" => 'check',
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