<?php
namespace App\Controller;

use App\Entity\{Payment, PaymentStatus};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/payments', name: 'admin_payments_')]
class AdminPaymentController extends AbstractController
{
    #[Route('', name: 'list')]
    public function list(EntityManagerInterface $em): Response
    {
        $payments = $em->getRepository(Payment::class)
            ->findBy([], ['status'=>'ASC','id'=>'DESC']);

        return $this->render('admin/payments/list.html.twig', [
            'payments' => $payments,
        ]);
    }

    
    #[Route('/approve/{id}', name: 'approve', methods: ['POST'])]
    public function approve(int $id, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $payment = $em->getRepository(Payment::class)->find($id);
        $approvedStatus = $em->getRepository(PaymentStatus::class)
            ->findOneBy(['code' => 'approved']);

        if ($payment && $approvedStatus) {
            $amount = floatval($request->request->get('approved_amount'));

            if ($amount > 0) {
                $payment->setStatus($approvedStatus);
                $payment->setApprovedAmount($amount);

                $user = $payment->getUser();
                $user->setBalance($user->getBalance() + $amount);

                $em->flush();
                $this->addFlash('success', "Заявка #{$id} одобрена на сумму {$amount}₽");
            } else {
                $this->addFlash('warning', "Сумма должна быть больше 0");
            }
        }

        return $this->redirectToRoute('admin_payments_list');
    }

    #[Route('/reject/{id}', name: 'reject')]
    public function reject(int $id, EntityManagerInterface $em): RedirectResponse
    {
        $payment = $em->getRepository(Payment::class)->find($id);
        $rejectedStatus = $em->getRepository(PaymentStatus::class)
            ->findOneBy(['code' => 'rejected']);

        if ($payment && $rejectedStatus) {
            $payment->setStatus($rejectedStatus);
            $em->flush();
            $this->addFlash('warning', "Заявка #{$id} отклонена");
        }

        return $this->redirectToRoute('admin_payments_list');
    }
}
