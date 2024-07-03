<?php

declare (strict_types=1);

namespace App\Helper;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartSessionHelper {
    
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var OrderRepository
     */
    private $cartRepository;

    /**
     * @var string
     */
    const CART_KEY_NAME = 'cart_id';

    /**
     * CartSessionHelper constructor.
     * @param OrderRepository $cartRepository
     */
    public function __construct(RequestStack $requestStack, OrderRepository $cartRepository) {
        
        $this->requestStack = $requestStack;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Gets the cart in session.
     * @return Order|null
     */
    public function getCart(): ?Order {
        return $this->cartRepository->findOneBy([
                    'id' => $this->getCartId(),
                    'status' => Order::STATUS_CART
        ]);
    }

    /**
     * Sets the cart in session.
     * @param Order $cart
     */
    public function setCart(Order $cart): void {
        $this->getSession()->set(self::CART_KEY_NAME, $cart->getId());
    }

    /**
     * Returns the cart id.
     * @return int|null
     */
    private function getCartId(): ?int {
        return $this->getSession()->get(self::CART_KEY_NAME);
    }

    private function getSession(): SessionInterface {
        return $this->requestStack->getSession();
    }
}
