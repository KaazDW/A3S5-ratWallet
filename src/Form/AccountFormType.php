<?php

namespace App\Form;

use App\Entity\Account;
use App\Entity\AccountType;
use App\Entity\Currency;
use App\Entity\User;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('balance', MoneyType::class, [
                'label' => 'Balance',
            ])
            ->add('user_id',  EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('name_account')
            ->add('currency', EntityType::class, [
                'class' => Currency::class,
                'choice_label' => 'label', // Nom du champ à afficher dans le formulaire
                'label' => 'Currency', // Étiquette du champ
            ])
            ->add('account_type', EntityType::class, [
                'class' => AccountType::class,
                'choice_label' => 'label',
                'label' => 'AccountType',
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
