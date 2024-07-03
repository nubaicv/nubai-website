<?php

declare (strict_types=1);

namespace App\Helper;

use App\Entity\Order;
use App\Factory\OrderFactory;
use App\Helper\CartSessionHelper;
use Doctrine\ORM\EntityManagerInterface;

class CartManagerHelper {

    /**
     * @var CartSessionStorage
     */
    private $cartSessionHelper;

    /**
     * @var OrderFactory
     */
    private $cartFactory;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(OrderFactory $cartFactory, CartSessionHelper $cartSessionHelper, EntityManagerInterface $em) {

        $this->cartSessionHelper = $cartSessionHelper;
        $this->cartFactory = $cartFactory;
        $this->em = $em;
    }

    /**
     * Gets the current cart.
     * 
     * @return Order
     */
    public function getCurrentCart(): Order {

        $cart = $this->cartSessionHelper->getCart();

        if (!$cart) {
            $cart = $this->cartFactory->create();
        }

        return $cart;
    }

    /**
     * Persists the cart in database and session.
     *
     * @param Order $cart
     */
    public function save(Order $cart): void {
        
        // Persist in database
        $this->em->persist($cart);
        $this->em->flush();
        // Persist in session
        $this->cartSessionHelper->setCart($cart);
    }
}
