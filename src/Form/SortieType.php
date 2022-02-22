<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut',DateTimeType::class,[
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('duree')
            ->add('dateLimiteInscription',DateTimeType::class,[
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('infosSortie')
            ->add('publier',SubmitType::class,[
                'label'=>'Publier'
            ])
            ->add('enregistrer',SubmitType::class,[
                'label'=>'Enregistrer'
            ])
            ->add('annuler',SubmitType::class,[
                'label'=>'Annuler'
            ])
            ->add('supprimer',SubmitType::class,[
                'label'=>'Supprimer la sortie'
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
