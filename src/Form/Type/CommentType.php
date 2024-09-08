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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CommentType.
 *
 * This class is responsible for building the form used for submitting comments.
 */
class CommentType extends AbstractType
{
    /**
     * CommentType constructor.
     *
     * @param TranslatorInterface $translator The translator service
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Builds the form for comments.
     *
     * @param FormBuilderInterface $builder The form builder interface
     * @param array                $options An array of options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => $this->translator->trans('Email'),
            ])
            ->add('content', TextareaType::class, [
                'label' => $this->translator->trans('Comment'),
            ]);
    }
}
