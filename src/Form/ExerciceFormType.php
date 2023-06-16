<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\Equipement;
use App\Entity\Exercice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ExerciceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomExercice')
            ->add('image', FileType::class, [
                'label' => 'image',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'],
                        'mimeTypesMessage' => 'Seules les images JPG, PNG et gif sont autorisées',

                    ]),
                ],
            ])
            ->add('duration')
            ->add('repetation')
            ->add('instruction', TextareaType::class)

            ->add('equipement', EntityType::class, [
                'class' => Equipement::class,
                'choice_label' => 'nomEquipement',
            ])
            ->add('ajouter_exercice', SubmitType::class, ['label' => 'Ajouter Exercice']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exercice::class,
        ]);
    }
}
