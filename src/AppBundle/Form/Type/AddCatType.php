<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 17.09.16
 * Time: 14:05
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\AddCat;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AddCatType extends AbstractType
{
    const NAME = 'add_cat';

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AddCat::class,
            'csrf_protection' => false
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', TextType::class, [
            'mapped' => false
        ]);
        $builder->add('url', UrlType::class);
    }

    public function getBlockPrefix()
    {
        return self::NAME;
    }
}