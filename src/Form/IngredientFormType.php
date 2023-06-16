<?php

namespace App\Form;

use App\Entity\IngredientEntity;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngredientFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom',
            'empty_data' => 0,
        ])
        ->add('quantite', IntegerType::class, [
           'label' => 'Quantité',
           'empty_data' => 0,
          
       ])
        ->add('calories', NumberType::class, [
            'label' => 'Calories',
            'empty_data' => 0,
        ])
        ->add('save', SubmitType::class, [
            'label' => 'Enregistrer'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => IngredientEntity::class,
        ]);
    }
}
