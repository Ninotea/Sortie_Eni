<?php

namespace App\Form;

use App\Donnee\DonneeSorties;
use App\Entity\Campus;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DonneeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $DateDuJour = new DateTime();
        $DateDuJour->setTime('12','0');
        $FDateDuJour = date_format($DateDuJour,'Y-m-d'.'\T'.'H:00');

        $builder
            ->add('mot', TextType::class,[
                'label'=>false,
                'required' => false,
                'attr'=>[
                    'placeholder'=>'Rechercher'
                ]])
            ->add('campus', EntityType::class,[
                'required' => false, // desactiver si besoin de filtrer obligatoriement les campus
                'label'=>'Campus : ',
                'class'=>Campus::class
            ])
            ->add('dateMin', DateTimeType::class,[
                'html5' => true,
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'id' => 'inputDateMin',
                    'value'=>$FDateDuJour
                    ],
            ])
            ->add('dateMax', DateTimeType::class,[
                'html5' => true,
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['id' => 'inputDateMax',
                    'value'=>$FDateDuJour
                ],
            ])
            ->add('organisateurTrue', CheckboxType::class,[
                'required' => false,
                'label' => "dont je suis l'organisateur",
                'attr'=>['class'=>'checkbox']
            ])
            ->add('organisateurFalse', CheckboxType::class,[
                'required' => false,
                'label' => "dont je ne suis pas l'organisateur",
                'attr'=>['class'=>'checkbox']
            ])
            ->add('inscritTrue', CheckboxType::class,[
                'required' => false,
                'label' => "auxquelles je suis inscrit",
                'attr'=>['class'=>'checkbox']
            ])
            ->add('inscritFalse', CheckboxType::class,[
                'required' => false,
                'label' => "auxquelles je ne suis pas inscrit",
                'attr'=>['class'=>'checkbox']
            ])
            ->add('passee', CheckboxType::class,[
                'required' => false,
                'label' => "passées",
                'attr'=>['class'=>'checkbox']
            ])
            ->add('ouverte', CheckboxType::class,[
                'required' => false,
                'label' => "ouverte",
                'attr'=>['class'=>'checkbox']
            ])
            ->add('creee', CheckboxType::class,[
                'required' => false,
                'label' => "Créée",
                'attr'=>['class'=>'checkbox']
            ])
            ->add('rechercher',SubmitType::class,[
                'attr'=>['class'=>'soumission'],
                'label_format'=>'Rechercher !'
            ])
        ;
    }
    // Configure les options liées au formulaire
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DonneeSorties::class, // quel class représente les données
            'method' => 'POST',
            //'csrf_protection'=>false //désactivation de la protectionn
        ]);
    }

    public function getBlockPrefix()
    {
        return ''; // Désactivation du préfix
        /*
         * Par défaut tout est mis dans un tableau appelé SearchData comme la class
         */
    }
}