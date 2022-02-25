<?php

namespace App\Form;

use App\Donnee\DonneeSorties;
use App\Entity\Campus;
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
        $builder
            ->add('mot', TextType::class,[
                'label'=>false,
                'required'=>false,
                'attr'=>[
                    'placeholder'=>'Rechercher'
                ]])
            ->add('campus', EntityType::class,[
                'label'=>'Campus : ',
                'required' => true,
                'class'=>Campus::class
            ])
            ->add('dateMin', DateTimeType::class,[
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('dateMax', DateTimeType::class,[
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('organisateurTrue', CheckboxType::class,[
                'label' => "dont je suis l'organisateur"
            ])
            ->add('organisateurFalse', CheckboxType::class,[
                'label' => "dont je ne suis pas l'organisateur"
            ])
            ->add('inscritTrue', CheckboxType::class,[
                'label' => "auxquelles je suis inscrit"
            ])
            ->add('inscritFalse', CheckboxType::class,[
                'label' => "auxquelles je ne suis pas inscrit"
            ])
            ->add('passee', CheckboxType::class,[
                'label' => "passées"
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
            'method' => 'GET', // on veut que la requette passe dans l'URL
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