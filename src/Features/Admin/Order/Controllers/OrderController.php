<?php

namespace App\Features\Admin\Order\Controllers;

class OrderController
{
    private $orderModel;
    private $authService;
    private $menuModel;

    public function __construct($orderModel, $authService, $menuModel)
    {
        $this->orderModel = $orderModel;
        $this->menuModel = $menuModel;
        $this->authService = $authService;
    }
    public function index()
    {
        $userInfo = $this->authService->getCurrentUser();
        $categories = $this->menuModel->getAllCategories();
        $availability = $this->menuModel->getAllAvailability();

        $filters = [
            'search' => $_GET['search'] ?? null,
            'category' => $_GET['category'] ?? null,
            'availability' => $_GET['availability'] ?? null
        ];

        $orders = $this->orderModel->getAllOrders();
        $menuItems = $this->menuModel->getFilteredItems($filters, 50, 0);

        require_once ROOT_PATH . 'src/Features/Public/Pos/View/pos.php';
    }
    public function history()
    {
        $filtersOrder = [
            'search' => $_GET['search'] ?? null,
            'from' => $_GET['from'] ?? null,
            'to' => $_GET['to'] ?? null
        ];

        $orders = $this->orderModel->getAllOrders($filtersOrder);

        $totalTransactions = count($orders);
        $lifetimeTotal = array_sum(array_column($orders, 'total_amount'));
        $avgOrder = $totalTransactions > 0 ? $lifetimeTotal / $totalTransactions : 0;

        $userInfo = $this->authService->getCurrentUser();


        require_once ROOT_PATH . 'src/Features/Admin/Order/View/history.php';
    }
    public function checkout()
    {
        // 1. Force JSON response header
        header('Content-Type: application/json');

        // 2. Read the raw JSON input from the fetch body
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data || empty($data['items'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid order data']);
            exit;
        }

        $currentUser = $this->authService->getCurrentUser();

        // 3. Prepare data for the Model
        $orderData = [
            'order_number' => 'ORD-' . strtoupper(bin2hex(random_bytes(3))),
            'user_id' => $currentUser['id'] ?? null,
            'total_amount' => (float) $data['total_amount'],
            'amount_paid' => (float) $data['amount_received'],
            'change_amount' => (float) ($data['amount_received'] - $data['total_amount']),
        ];

        // 4. Delegate to Model (which handles the Transaction)
        $result = $this->orderModel->createOrder($orderData, $data['items']);

        if ($result) {
            echo json_encode(['success' => true, 'order_id' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error during checkout']);
        }
        exit;
    }
}