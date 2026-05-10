<?php
require_once __DIR__ . '/../../../autoload.php';

use App\Shared\Components\Button\Button;
use App\Shared\Components\InputField\InputField;

$pageTitle = 'Sign Up | Cafe 404';

ob_start();
?>

<main class="flex h-screen justify-center items-center flex-1">
    <div class="w-full max-w-md mx-auto flex flex-col gap-6">
        <div class="card-header flex flex-col  items-center">
            <img src="/cafe_404/public/assets/images/logo/logo.png" alt="Coffee 404 Logo" class="w-64">
            <div class="text-center serif-display tracking-tight text-[#002c02]">
                <h1 class="text-4xl text-title text-primary">Cafe 404</h1>
                <span class="text-subtitle">Coffee not found!</span>
            </div>
        </div>
        <form method="POST" action="/cafe_404/login">
            <card class="w-full max-w-md bg-white rounded-md flex flex-col gap-8 p-8">
                <card.content class="">
                    <div class="flex gap-5 flex-col">

                        <?= InputField::render([
                            'label' => "Admin Username",
                            'name' => "adminUsername",
                            'placeholder' => "Enter your username",
                            'leading' => 'user',

                        ]); ?>
                        <?= InputField::render([
                            'label' => 'Admin Password',
                            'name' => "adminPassword",
                            'type' => "password",
                            'placeholder' => "Enter your password",
                            'leading' => 'lock',
                        ]); ?>
                    </div>


                </card.content>
                <card.footer class="flex flex-col gap-2.5">
                    <div class="text-sm text-center">
                        <?= Button::render("Access System", [
                            "type" => "submit",
                            "trailing" => 'arrow-right',
                            'variant' => 'primary',
                        ]); ?>
                        
                    </div>
                </card.footer>
        </form>
        </card>
    </div>
</main>

<?php
$content = ob_get_clean();

require_once __DIR__ . '/../../../Shared/Layouts/layout.php';
?>