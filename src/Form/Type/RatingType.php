<?php
/**
 * This file is part of the [Your Application Name] application.
 *
 * (c) [Your Name] <your.email@example.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class RatingType.
 *
 * This class is responsible for building the form used for rating entities.
 */
class RatingType extends AbstractType
{
    /**
     * RatingType constructor.
     *
     * @param TranslatorInterface $translator The translator service
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Builds the form for rating.
     *
     * @param FormBuilderInterface $builder The form builder interface
     * @param array                $options An array of options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', IntegerType::class, [
                'label' => $this->translator->trans('Rating'),
                'attr' => ['min' => 1, 'max' => 10],
            ]);
    }

    /**
     * Configures the options for this form.
     *
     * @param OptionsResolver $resolver The options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'is_authenticated' => false,
        ]);
    }
}
