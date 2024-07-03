<?php

declare (strict_types=1);

namespace App\Factory;

use App\Entity\Order;
use App\Entity\OrderItem;
use Bolt\Entity\Content;

/**
 * Class OrderFactory
 * @package App\Factory
 */
class OrderFactory {

    /**
     * Creates an order.
     *
     * @return Order
     */
    public function create(): Order {
        $order = new Order();
        $order
                ->setStatus(Order::STATUS_CART)
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime());

        return $order;
    }

    /**
     * Creates an item for a product.
     *
     * @param Product $product
     *
     * @return OrderItem
     */
    public function createItem(Content $service): OrderItem {
        
//        if ($product->getContentType() !== 'products') {
//            
//            throw new \Exception('No product');
//        }
        $item = new OrderItem();
        $item->setProduct($service);
        $item->setQuantity(1);

        return $item;
    }
}
