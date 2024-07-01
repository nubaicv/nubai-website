<?php

namespace App\Controller;

use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\Controller\TwigAwareController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShoppingCartController extends TwigAwareController
{
    #[Route('/{_locale}/my-cart', name: 'shopping_cart_nubai')]
    public function index(): Response
    {
        return $this->render('@theme/cart.twig', [
            'controller_name' => 'ShoppingCartController',
            'data' => []
        ]);
    }
}
