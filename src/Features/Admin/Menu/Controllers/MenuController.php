<?php

namespace App\Features\Admin\Menu\Controllers;

class MenuController
{
    private $menuModel;
    private $authService;
    public function __construct($menuModel, $authService, )
    {
        $this->menuModel = $menuModel;
        $this->authService = $authService;
    }
    public function index()
    {
        $adminInfo = $this->authService->getCurrentUser();
        $menuItems = $this->menuModel->getAllItems();

        $filters = [
            'search' => $_GET['search'] ?? null,
            'category' => $_GET['category'] ?? null,
            'availability' => $_GET['availability'] ?? null
        ];

        $limit = 6;
        $currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $offset = ($currentPage - 1) * $limit;

        $totalItems = $this->menuModel->getFilteredCount($filters);
        $totalPages = ceil($totalItems / $limit);
        $menuItems = $this->menuModel->getFilteredItems($filters, $limit, $offset);

        $categories = $this->menuModel->getAllCategories();
        $availability = $this->menuModel->getAllAvailability();
        $inStockCount = $this->menuModel->getInStockCount();
        $totalCount = $this->menuModel->getTotalCount();

        require_once ROOT_PATH . 'src/Features/Admin/Menu/View/menu.php';
    }
    public function create()
    {
        $adminInfo = $this->authService->getCurrentUser();
        $categories = $this->menuModel->getAllCategories();
        $availability = $this->menuModel->getAllAvailability($_POST['is_available'] ?? '');

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
        $availability = $this->menuModel->getAllAvailability();

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
                $prefix = 'CB-';
            }

            $productCode = $prefix . strtoupper(substr(uniqid(), -5));

            $imageName = 'default-coffee.jpg';
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['product_image']['tmp_name'];
                $fileName = $_FILES['product_image']['name'];
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $imageName = uniqid('menu_', true) . '.' . $extension;
                $uploadDir = ROOT_PATH . 'public/assets/images/uploads/menu/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (!move_uploaded_file($fileTmpPath, $uploadDir . $imageName)) {
                    die("Error: Could not move uploaded file to " . $uploadDir);
                }
            }
            $posX = $_POST['product_image_pos_x'] ?? 50.00;
            $posY = $_POST['product_image_pos_y'] ?? 50.00;

            $data = [
                'product_code' => $productCode,
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'price' => (float) $_POST['price'],
                'category_id' => $categoryId,
                'image_path' => $imageName,
                'pos_x' => (float) $posX,
                'pos_y' => (float) $posY,
                'is_available' => $_POST['is_available'] ?? 1,
            ];
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

            $categoryId = $_POST['category_id'] ?? $existingItem['category_id'];
            $productCode = $existingItem['product_code'];

            if ($categoryId != $existingItem['category_id']) {
                $category = $this->menuModel->getCategoryById($categoryId);
                $categoryName = strtolower($category['category_name'] ?? '');

                if (str_contains($categoryName, 'espresso')) {
                    $prefix = 'ESP-';
                } elseif (str_contains($categoryName, 'pastry')) {
                    $prefix = 'PAS-';
                } else {
                    $prefix = 'CB-';
                }

                $productCode = $prefix . strtoupper(substr(uniqid(), -5));
            }

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
                'product_code' => $productCode,
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'price' => (float) $_POST['price'],
                'category_id' => $categoryId,
                'image_path' => $imageName,
                'pos_x' => $_POST['product_image_pos_x'] ?? $existingItem['pos_x'],
                'pos_y' => $_POST['product_image_pos_y'] ?? $existingItem['pos_y'],
                'is_available' => $_POST['is_available'] ?? $existingItem['is_available'],
            ];

            if ($this->menuModel->updateItem($id, $data)) {
                header('Location: /cafe_404/menu?updated=1');
                exit;
            }
        }
    }
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if ($this->menuModel->deleteItem($id)) {
                header('Location: /cafe_404/menu?deleted=1');
                exit;
            } else {
                header('Location: /cafe_404/menu?error=delete_failed');
                exit;
            }
        }
    }
}