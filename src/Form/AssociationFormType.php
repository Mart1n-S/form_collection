<?php

namespace App\Form;

use App\Entity\Action;
use App\Form\MembreType;
use App\Entity\Association;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\ActionRepository;
use Symfony\Component\Form\AbstractType;
use PhpParser\Node\Scalar\MagicConst\Dir;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AssociationFormType extends AbstractType
{
    private $actionRepository;

    public function __construct(ActionRepository $actionRepository)
    {
        $this->actionRepository = $actionRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('action', EntityType::class, [
                'class' => Action::class,
                'choice_label' => fn(Action $action) => $action->getNameAction(),
                'multiple' => false,
                'expanded' => true,
                'choice_attr' => function ($action, $key, $value) {
                    return [
                        'class' => 'btn-check',
                        'id'    => 'action_' . $action->getId(),
                        'autocomplete' => 'off'
                    ];
                },
                'label_attr' => ['class' => 'action-card'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Association::class,
        ]);
    }
}
