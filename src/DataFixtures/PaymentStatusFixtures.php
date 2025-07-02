<?php
namespace App\DataFixtures;

use App\Entity\PaymentStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PaymentStatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statuses = [
            ['code' => 'pending',  'label' => 'Ожидает'],
            ['code' => 'approved', 'label' => 'Одобрено'],
            ['code' => 'rejected', 'label' => 'Отклонено'],
        ];

        foreach ($statuses as $def) {
            $st = new PaymentStatus();
            $st->setCode($def['code'])
                ->setLabel($def['label']);
            $manager->persist($st);
        }

        $manager->flush();
    }
}
