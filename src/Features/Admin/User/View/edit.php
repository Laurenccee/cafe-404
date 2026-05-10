<?php
use App\Shared\Components\Button\Button;
use App\Shared\Components\InputField\InputField;
use App\Shared\Components\ComboBox\ComboBoxField;

$pageTitle = 'Edit Barista | Cafe 404';

$roles = $roles ?? [];

ob_start();
?>

<main class="flex h-screen justify-center items-center flex-1 bg-background">
    <div class="w-full max-w-md mx-auto flex flex-col gap-6">

        <div class="card-header flex flex-col gap-4 items-center">
            <h1 class="text-7xl text-title text-primary italic serif-display">Edit Account</h1>
            <span class="text-subtitle">Updating Account
                <span class="font-black text-title uppercase "><?= htmlspecialchars($user['username']) ?></span>
            </span>
        </div>

        <form method="POST" action="/cafe_404/users/update">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">

            <div class="w-full max-w-md bg-white rounded-md flex flex-col gap-8 p-8">

                <div class="flex gap-6 flex-col">
                    <?= InputField::render([
                        'label' => "Username",
                        'name' => "username",
                        'value' => htmlspecialchars($user['username']),
                        'placeholder' => "Enter username",
                        'leading' => 'user',
                    ]); ?>
                    <?= ComboBoxField::render([
                        "name" => "role",
                        "label" => "User Role",
                        "value" => $user['role_id'],
                        "options" => array_merge(

                            $roles ?? []
                        ),
                    ]) ?>
                    <div class="pt-4 border-t border-secondary/5">
                        <p class="text-[10px] uppercase tracking-[0.2em] font-bold text-secondary mb-4">Security Update
                        </p>
                        <?= InputField::render([
                            'label' => 'New Password',
                            'name' => "password",
                            'type' => "password",
                            'placeholder' => "Leave blank to keep current",
                            'leading' => 'lock',
                        ]); ?>
                    </div>
                </div>

                <div class="flex flex-col gap-6">
                    <?= Button::render("Save Changes", [
                        "type" => 'submit',
                        "trailing" => 'check',
                        'variant' => 'primary',
                    ]); ?>

                    <a href="/cafe_404/users"
                        class="flex justify-center items-center gap-2 text-xs font-bold uppercase tracking-widest text-secondary/50 hover:text-red-500 transition-colors">
                        <i data-lucide="arrow-left" class="size-3"></i>
                        Back to Users
                    </a>
                </div>
            </div>
        </form>
    </div>
</main>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . 'src/Shared/Layouts/layout.php';