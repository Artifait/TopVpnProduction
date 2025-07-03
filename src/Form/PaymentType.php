<?php
namespace App\Form;

use App\Entity\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{TextType, MoneyType, FileType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o)
    {
        $b
            ->add('sender', TextType::class, [
                'label' => 'От кого',
                'attr'  => ['placeholder' => 'Александр А. С.'],
            ])
            ->add('requestedAmount', MoneyType::class, [
                'label'    => 'Сумма',
                'currency' => 'RUB',
                'attr'     => ['placeholder' => '300.00'],
            ])
            ->add('receiptPath', FileType::class, [
                'label'       => 'Квитанция (необязательно)',
                'required'    => false,
                'mapped'      => false,
                'constraints' => [
                    new File([
                        'maxSize'          => '2M',
                        'mimeTypes'        => ['image/*','application/pdf'],
                        'mimeTypesMessage' => 'Загрузите изображение или PDF',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $r)
    {
        $r->setDefaults([
            'data_class' => Payment::class,
        ]);
    }
}
