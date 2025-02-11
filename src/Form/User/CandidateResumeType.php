<?php

namespace App\Form;

use App\Entity\CandidateResume;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidateResumeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ResumeHeadline', TextareaType::class)
            ->add('Skills', TextType::class)
            ->add('Experience', TextType::class)

            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])

            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success'  ]
            ])
          /**  ->add('userId', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])*/
        ;
    }

        public
        function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => CandidateResume::class,
            ]);
        }
    }
