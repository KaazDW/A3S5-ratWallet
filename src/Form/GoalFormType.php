<?php

namespace App\Form;

use App\Entity\AccountType;
use App\Entity\Category;
use App\Entity\Goal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GoalFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('targetAmount')
            ->add('deadline')
            ->add('description')
           /* ->add('category_id', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'label',
                'label' => 'Category',
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Goal::class,
        ]);
    }
}
