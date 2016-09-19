<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 17.09.16
 * Time: 17:25
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\ChangeCat;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class ChangeCatType extends AbstractType
{
    const NAME = 'change_cat';

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'PATCH',
            'data_class' => ChangeCat::class,
            'csrf_protection' => false
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('url', UrlType::class);
    }

    public function getBlockPrefix()
    {
        return self::NAME;
    }
}