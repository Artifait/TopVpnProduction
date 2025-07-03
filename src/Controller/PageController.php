<?php

namespace App\Controller;

use App\Entity\{Payment, PaymentStatus, User};
use App\Form\PaymentType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, RedirectResponse};
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function home(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('page/home.html.twig', 
            ['balance' => $user->getBalance()]
        );
    }

    #[Route('/products', name: 'app_products')]
    public function products(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('page/products.html.twig', 
            ['balance' => $user->getBalance()]
        );
    }

    #[Route('/balance', name: 'app_balance')]
    public function balance(Request $req, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $payment = new Payment();
        $payment->setUser($user);

        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($req);

        // Проверить наличие существующей pending-заявки
        $pendingStatus = $em->getRepository(PaymentStatus::class)->findOneBy(['code' => 'pending']);
        $existing = $em->getRepository(Payment::class)->findOneBy(['user' => $user, 'status' => $pendingStatus]);

        $payment->setStatus($pendingStatus);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($existing) {
                $this->addFlash('warning', 'У вас уже есть незавершённая заявка.');
                return $this->redirectToRoute('app_balance');
            }

            // Обработка файла квитанции
            $file = $form->get('receiptPath')->getData();
            if ($file) {
                $newName = uniqid().'.'.$file->guessExtension();
                $file->move($this->getParameter('receipts_dir'), $newName);
                $payment->setReceiptPath($newName);
            }
            $payment->setCreatedAt(new DateTimeImmutable());

            $em->persist($payment);
            $em->flush();

            $this->addFlash('success', 'Заявка отправлена и ожидает модерации');
            return $this->redirectToRoute('app_balance');
        }

        // Список своих заявок
        $pending = $em->getRepository(Payment::class)
            ->findBy(['user' => $user], ['id'=>'DESC']);
            
        if ($existing) {
            $this->addFlash('warning', 'У вас уже есть платёж на модерации.');
            return $this->render('page/balance.html.twig', [
                'balance' => $user->getBalance(),
                'pending' => $pending,
                'form'    => null,
            ]);
        }
        
        return $this->render('page/balance.html.twig', [
            'balance' => $user->getBalance(),
            'form'    => $form->createView(),
            'pending' => $pending,
        ]);
    }
}
