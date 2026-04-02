<?php
use App\Shared\Components\Sidebar\Sidebar;
use App\Shared\Components\Button\Button;
use App\Shared\Components\ComboBox\ComboBoxField;
use App\Shared\Components\InputField\InputField;
use App\Shared\Components\Badge;

$pageTitle = 'The Editorial Barista | Management';
$now = new DateTimeImmutable();
ob_start();
?>

<div class="flex  h-screen overflow-hidden bg-background">
    <?= Sidebar::render($adminInfo['username']) ?>

    <main class="flex-1 flex overflow-y-auto smooth-scroll flex-col gap-12 py-14 px-16">
        <header class="flex justify-between items-end">
            <div class="max-w-full">
                <h1 class="serif-display text-title text-7xl">User Management</h1>
                <p class=" opacity-70 font-serif text-subtitle ">
                    Access Management.
                </p>
            </div>
        </header>
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
            <div class="col-span-2 bg-white rounded-xl p-8 flex flex-col gap-8 ">
                <h3 class=" font-bold text-lg uppercase opacity-60 tracking-[0.2em]">Add new user</h3>
                <form method="POST" action="/cafe_404/users/add">
                    <div class="flex flex-col gap-8">
                        <div class="flex flex-col gap-4">
                            <?= InputField::render([
                                'label' => "Username",
                                'name' => "username",
                                'placeholder' => "Enter username",
                                'leading' => 'user',

                            ]); ?>
                            <?= InputField::render([
                                'label' => 'Password',
                                'name' => "password",
                                'type' => "password",
                                'placeholder' => "Enter your password",
                                'leading' => 'lock',
                            ]); ?>
                            <?= InputField::render([
                                'label' => 'Confirm Password',
                                'name' => "confirmPassword",
                                'type' => "password",
                                'placeholder' => "Enter your password",
                                'leading' => 'lock',
                            ]); ?>
                            <?= ComboBoxField::render([
                                'label' => 'Role',
                                'name' => "role",
                                "options" => array_merge(
                                    [
                                        ["value" => "", "label" => "Select Role"]
                                    ],
                                    $roles ?? []
                                ),
                            ]); ?>
                        </div>
                        <div>
                            <?= Button::render("Add User", [
                                "trailing" => 'arrow-right',
                                'variant' => 'primary',
                            ]); ?>
                        </div>
                    </div>

                </form>
            </div>
            <div class="col-span-3 rounded-xl p-8 flex flex-col gap-6 ">
                <h3 class=" font-bold text-title font-sans-serif text-lg uppercase tracking-[0.2em]">
                    Registered Users
                </h3>
                <div class="flex flex-col gap-4">
                    <?php foreach ($users as $user):
                        $isActiveUser = ((int) $user['id'] === (int) $adminInfo['id']);
                        ?>

                        <div class="flex items-center justify-between p-6 rounded-md transition-all 
                            <?= $isActiveUser ? 'bg-accent' : 'bg-white' ?>">
                            <div class="flex flex-1 items-center gap-8">
                                <i data-lucide="user" class="size-8 text-secondary"></i>
                                <div class="flex-1">
                                    <div class="flex gap-4 items-center">
                                        <p class="font-semibold text-title capitalize">
                                            <?= htmlspecialchars($user['username']) ?>
                                        </p>
                                        <?= Badge::render(ucfirst($user['role_name']), $user['role_id']) ?>
                                    </div>
                                    <div flex class="flex gap-2">
                                        <p class="text-sm text-subtitle ">Registered on
                                            <?= (new DateTimeImmutable($user['created_at']))->format('F d, Y') ?>
                                        </p>
                                        <?= $isActiveUser ? "<p class='text-sm text-[#6f4e37]/50 font-semibold'>Currently logged in</p>" : "" ?>
                                    </div>
                                </div>

                                <div class="flex gap-2 transition-opacity">
                                    <a href="/cafe_404/users/edit?id=<?= $user['id'] ?>"
                                        class="p-2 hover:bg-emerald-50 rounded-md transition-colors text-secondary hover:text-primary"
                                        title="Edit User">
                                        <i data-lucide="edit" class="size-5"></i>
                                    </a>

                                    <a href="/cafe_404/users/delete?id=<?= $user['id'] ?>"
                                        class="p-2 hover:bg-red-50 rounded-md transition-colors text-secondary hover:text-red-500"
                                        title="Delete User"
                                        onclick="return confirm('Are you sure you want to remove this user from the Ledger?');">
                                        <i data-lucide="trash" class="size-5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . 'src/Shared/Layouts/layout.php';


?>