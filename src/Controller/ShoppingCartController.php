<?php

declare (strict_types=1);

namespace App\Controller;

use App\Factory\OrderFactory;
use App\Helper\CartManagerHelper;
use Bolt\Enum\Statuses;
use Bolt\Storage\Query;
use Bolt\Controller\TwigAwareController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShoppingCartController extends TwigAwareController {
    
    /**
     * @var Query
     */
    private $query;
    
    /**
     * @var CartManagerHelper
     */
    private $cartManager;
    
    public function __construct(Query $query, CartManagerHelper $cartManager) {
        
        $this->query = $query;
        $this->cartManager = $cartManager;
    }
    
    #[Route('/{_locale}/my-cart', name: 'shopping_cart_nubai')]
    public function index(Request $request): Response
    {
        
        $product1 = $this->query->getContent('products', ['id' => 22]);
        $product2 = $this->query->getContent('products', ['id' => 22]);
        
        $cart = $this->cartManager->getCurrentCart();
        
        return $this->render('@theme/cart.twig', [
            'controller_name' => 'ShoppingCartController',
            'cart' => $cart
        ]);
    }
}
