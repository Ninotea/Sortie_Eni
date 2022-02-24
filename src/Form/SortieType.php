<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('infosSortie')
            ->add('duree')
            ->add('lieu', EntityType::class,[
                'class'=> Lieu::class,
                'choice_label'=>'nom'
            ])
            ->add('nbInscriptionsMax',IntegerType::class,[
                'label'=>'Nombre d\'inscription maximum'
            ])
            ->add('dateHeureDebut',DateTimeType::class,[
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('dateLimiteInscription',DateTimeType::class,[
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('publier',SubmitType::class,[
                'attr'=>['class'=>'soumission'],
                'label_format'=>'Publier'
            ])
            ->add('enregistrer',SubmitType::class,[
                'attr'=>['class'=>'soumission'],
                'label_format'=>'Enregistrer'
            ])
            ->add('annuler',SubmitType::class,[
                'attr'=>['class'=>'soumission'],
                'label_format'=>'Annuler'
            ])
            ->add('supprimer',SubmitType::class,[
                'attr'=>['class'=>'soumission'],
                'label_format'=>'Supprimer la sortie'
            ])
            ->add('ajouterLieu',SubmitType::class,[
                'attr'=>['class'=>'soumission'],
                'label_format'=>'Ajouter un lieu'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
