<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;

class BalanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('balance', MoneyType::class, [
                'currency' => 'USD',
                'data' => 0,
                'constraints' => [
                    new Range([
                        'min' => 0.3,
                        'max' => 1000,
                    ]),
                ],
            ]);
        
    }
}
