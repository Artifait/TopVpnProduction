<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('page/home.html.twig');
    }

    #[Route('/products', name: 'app_products')]
    public function products(): Response
    {
        return $this->render('page/products.html.twig');
    }

    #[Route('/balance', name: 'app_balance')]
    public function balance(): Response
    {
        // здесь позже передадим реальный баланс по вычислениям или из БД
        return $this->render('page/balance.html.twig', [
            'balance' => 0,
        ]);
    }
}
