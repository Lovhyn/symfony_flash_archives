<?php

namespace App\Form;

use Bartender;
use App\Entity\Tag;
use App\Entity\Tasks;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityRepository;
use PhpParser\Node\Expr\Cast\Bool_;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class TaskType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var StatusRepository
     */
    private $repository;


    public function __construct(TranslatorInterface $translator, StatusRepository $repository)
    {
        $this->translator = $translator;
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $bartender = new Bartender();
        $filteredBeerListNameName = $bartender->filterBeerList();
        $listStatus = $this->repository->findAll();

        $builder
            ->add('name', ChoiceType::class, [
                'choices' => $filteredBeerListNameName,
                'label' => $this->translator->trans('general.name')
            ])
            ->add('description', TextareaType::class, [
                'label' => $this->translator->trans('general.description')
            ])
            ->add('dueAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => $this->translator->trans('general.due_date')
            ])
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name'
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    $this->translator->trans("general.status.1") => $this->repository->findAll()[3],
                    $this->translator->trans("general.status.2") => $this->repository->findAll()[4],
                    $this->translator->trans("general.status.3") => $this->repository->findAll()[5]
                ],
                'label' => $this->translator->trans("general.status.title"),
                'expanded' => false,
                'multiple' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => $this->translator->trans('general.button.success'),
                'attr' => [
                    'class' => 'btn-dark'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tasks::class,
        ]);
    }
}
