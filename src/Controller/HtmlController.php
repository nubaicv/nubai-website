<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HtmlController extends AbstractController
{
    #[Route('/html', name: 'app_html')]
    public function index(): Response
    {
        return new Response('<h1>Hello Vladu!</h1>', 200);
    }
}
