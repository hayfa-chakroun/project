<?php
// src/Form/CustomAuthorSearchType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomAuthorSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('minBooks', IntegerType::class, [
                'required' => false,
                'label' => 'Minimum Number of Books',
            ])
            ->add('maxBooks', IntegerType::class, [
                'required' => false,
                'label' => 'Maximum Number of Books',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null, // You can change this to match your entity
        ]);
    }
}
