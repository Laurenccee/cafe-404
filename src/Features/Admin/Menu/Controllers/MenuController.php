<?php

namespace App\Features\Admin\Menu\Controllers;

class MenuController
{
    private $menuModel;
    private $authService;

    public function __construct($menuModel, $authService)
    {
        $this->menuModel = $menuModel;
        $this->authService = $authService;
    }

    public function index()
    {
        $adminInfo = $this->authService->getCurrentUser();
        $menuItems = $this->menuModel->getAllItems();

        $categories = $this->menuModel->getAllCategories();

        $limit = 5;
        $currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $offset = ($currentPage - 1) * $limit;

        $totalItems = $this->menuModel->getTotalCount();
        $totalPages = ceil($totalItems / $limit);

        $menuItems = $this->menuModel->getItemsPaginated($limit, $offset);
        $inStockCount = $this->menuModel->getInStockCount();



        require_once ROOT_PATH . 'src/Features/Admin/Menu/View/menu.php';
    }

    public function create()
    {
        $adminInfo = $this->authService->getCurrentUser();
        $categories = $this->menuModel->getAllCategories();

        require_once ROOT_PATH . 'src/Features/Admin/Menu/View/add.php';
    }
    public function edit($id)
    {
        $item = $this->menuModel->getItemById($id);

        if (!$item) {
            header('Location: /cafe_404/admin/menu?error=not_found');
            exit;
        }

        $adminInfo = $this->authService->getCurrentUser();
        $item = $this->menuModel->getItemById($id);
        $categories = $this->menuModel->getAllCategories();


        if (!$item) {
            header('Location: /cafe_404/admin/menu?error=not_found');
            exit;
        }

        require_once ROOT_PATH . 'src/Features/Admin/Menu/View/edit.php';
    }
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryId = $_POST['category_id'] ?? '';

            $category = $this->menuModel->getCategoryById($categoryId);
            $categoryName = strtolower($category['category_name'] ?? '');

            if (str_contains($categoryName, 'espresso')) {
                $prefix = 'ESP-';
            } elseif (str_contains($categoryName, 'pastry')) {
                $prefix = 'PAS-';
            } else {
                $prefix = 'CB-'; // Non-Coffee / Cold Brew
            }

            $productCode = $prefix . strtoupper(substr(uniqid(), -5));

            // 2. Handle Image Upload
            $imageName = 'default-coffee.jpg'; // Fallback image
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['product_image']['tmp_name'];
                $fileName = $_FILES['product_image']['name'];
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $imageName = uniqid('menu_', true) . '.' . $extension;
                $uploadDir = ROOT_PATH . 'public/assets/images/uploads/menu/';

                // Ensure directory exists
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (!move_uploaded_file($fileTmpPath, $uploadDir . $imageName)) {
                    // ONLY die if the actual movement fails
                    die("Error: Could not move uploaded file to " . $uploadDir);
                }

                move_uploaded_file($fileTmpPath, $uploadDir . $imageName);
            }

            // 3. Capture Drag Coordinates (from hidden inputs in FileDrop)
            $posX = $_POST['product_image_pos_x'] ?? 50.00;
            $posY = $_POST['product_image_pos_y'] ?? 50.00;

            // 4. Prepare Data for Model
            $data = [
                'product_code' => $productCode,
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'price' => (float) $_POST['price'],
                'category_id' => $categoryId,
                'image_path' => $imageName, // Renamed from image_url to match DB
                'pos_x' => (float) $posX,
                'pos_y' => (float) $posY
            ];

            // 5. Save and Redirect
            if ($this->menuModel->addItem($data)) {
                header('Location: /cafe_404/menu?success=1');
                exit;
            }
        }
    }
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $existingItem = $this->menuModel->getItemById($id);
            $imageName = $existingItem['image_path'];
            $uploadDir = ROOT_PATH . 'public/images/uploads/menu/';

            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['product_image']['tmp_name'];
                $extension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));

                $newImageName = uniqid('menu_', true) . '.' . $extension;

                if (move_uploaded_file($fileTmpPath, $uploadDir . $newImageName)) {
                    if ($existingItem['image_path'] !== 'default-coffee.jpg') {
                        @unlink($uploadDir . $existingItem['image_path']);
                    }
                    $imageName = $newImageName;
                }
            }

            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'price' => (float) $_POST['price'],
                'category_id' => $_POST['category_id'],
                'image_path' => $imageName,
                'pos_x' => $_POST['product_image_pos_x'] ?? $existingItem['pos_x'],
                'pos_y' => $_POST['product_image_pos_y'] ?? $existingItem['pos_y']
            ];

            if ($this->menuModel->updateItem($id, $data)) {
                header('Location: /cafe_404/menu?updated=1');
                exit;
            }
        }
    }
}