<?php

namespace App\Features\Admin\Dashboard\Controllers;

class DashboardController
{
    private $authService;
    private $orderModel;

    public function __construct($authService, $orderModel)
    {
        $this->authService = $authService;
        $this->orderModel = $orderModel;
    }

    public function index()
    {
        $adminInfo = $this->authService->getCurrentUser();

        $allOrders = $this->orderModel->getAllOrders() ?: [];
        $popularOrders = $this->orderModel->getPopularItems(4) ?: [];

        $todayStr = (new \DateTime())->format('Y-m-d');
        $monthStr = (new \DateTime())->format('Y-m');

        $todaysOrders = array_filter($allOrders, function ($order) use ($todayStr) {
            return str_starts_with($order['created_at'], $todayStr);
        });

        $monthlyOrders = array_filter($allOrders, function ($order) use ($monthStr) {
            return str_starts_with($order['created_at'], $monthStr);
        });

        $todayIncome = array_sum(array_column($todaysOrders, 'total_amount'));
        $todayCount = count($todaysOrders);

        $monthlyIncome = array_sum(array_column($monthlyOrders, 'total_amount'));
        $monthlyCount = count($monthlyOrders);

        $recentOrders = array_slice($allOrders, 0, 5);

        require_once ROOT_PATH . 'src/Features/Admin/Dashboard/View/dashboard.php';
    }
}