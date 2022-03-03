<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class,[
                'label'=>'Votre pseudo : '
            ])
            ->add('leCampus',EntityType::class,[
                'class'=> Campus::class,
                'choice_label'=>'nom',
                'label'=>'Campus : ',
                'required' => true,
            ])
            ->add('prenom', TextType::class,[
                'label'=>'Prenom : '
            ])
            ->add('nom', TextType::class,[
                'label'=>'Nom : '
            ])
            ->add('telephone',TextType::class,[
                'label'=>'Téléphone : '
            ])
            ->add('email', TextType::class,[
                'label'=>'Email : '
            ])
            ->add('plainPassword', RepeatedType::class, [
                // Ajouts pour le type Repeated
                // Doivent être placés au début
                'type' => PasswordType::class,
                'invalid_message' => 'Les valeurs pour les champs mots de passe doivent être identiques.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Répétez le mot de passe'],

                // Code généré par la commande 'make:registration-form'
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Mot de passe obligatoire',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit avoir une longueur minimum de {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('administrateur')
            ->add('actif')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('Modifier',SubmitType::class,[
                'label_format'=>'Modifier'
            ])
            ->add('Desactiver',SubmitType::class,[
                'label_format'=>'Desactiver'
            ])
            ->add('Supprimer',SubmitType::class,[
                'label_format'=>'Supprimer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
