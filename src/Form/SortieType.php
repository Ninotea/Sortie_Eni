<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $DateDuJour = new DateTime();
        $DateDuJour->setTime('12','0');
        $FDateDuJour = date_format($DateDuJour,'Y-m-d'.'\T'.'H:00');

        $builder
            ->add('nom')
            ->add('infosSortie')
            ->add('duree',NumberType::class,[
                'label'=>'Durée'
            ])
            ->add('dureeH',TimeType::class,[
                'label'=>'Durée : ',
                'html5' => true,
                'widget' => 'single_text',
                'input_format'=>'H/i',
                'attr'=>['value' => "02:30"],
            ])
            ->add('lieu', EntityType::class,[
                'class'=> Lieu::class,
                'choice_label'=>'nom'
            ])
            ->add('nbInscriptionsMax',IntegerType::class,[
                'label'=>'Nombre d\'inscription maximum : '
            ])
            ->add('dateHeureDebut',DateTimeType::class,[
                'html5' => true,
                'widget' => 'single_text',
                'attr' => ['value'=>$FDateDuJour],
            ])
            ->add('dateLimiteInscription',DateTimeType::class,[
                'html5' => true,
                'widget' => 'single_text',
                'attr' => ['value'=>$FDateDuJour],
            ])
            ->add('publier',SubmitType::class,[
                'label_format'=>'Publier'
            ])
            ->add('enregistrer',SubmitType::class,[
                'label_format'=>'Enregistrer'
            ])
            ->add('annuler',SubmitType::class,[
                 'label_format'=>'Annuler'
            ])
            ->add('supprimer',SubmitType::class,[
                'label_format'=>'Supprimer la sortie'
            ])
            ->add('ajouterLieu',SubmitType::class,[
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
