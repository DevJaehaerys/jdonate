<?php
// https://github.com/DevJaehaerys/jdonate
class ShopCard
{
    private $db;
    public function __construct() {
        $this->db = new Database();
    }

    public function displayProducts() {
        try {
            $products = $this->db->getProducts();
        } catch (PDOException $e) {
            die("db error");
        }
        $html = '';
        foreach ($products as $product) {
            $html .= '<div class="bg-white rounded-lg border p-4">';
            $html .= '<img src="' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '" class="w-full h-auto rounded-md object-cover">';
            $html .= '<div class="px-1 py-4">';
            $html .= '<div class="font-bold text-xl mb-2">' . htmlspecialchars($product['name']) . '</div>';
            $html .= '<p class="text-gray-700 text-base">' . htmlspecialchars($product['descr']) . '</p>';
            $html .= '</div>';
            $html .= '<div class="px-1 py-4">';
            $html .= '<button onclick="buyItem(' . htmlspecialchars($product['id']) . ')" class="text-blue-500 hover:underline button button-shop">Buy</button>';
            $html .= '</div>';
            $html .= '</div>';
        }
        return $html;
    }
}
