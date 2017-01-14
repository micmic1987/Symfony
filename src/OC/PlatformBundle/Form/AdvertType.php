<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OC\PlatformBundle\Repository\CategoryRepository;
use OC\PlatformBundle\Repository\AdvertSkillRepository;
use OC\PlatformBundle\Repository\SkillRepository;
use OC\PlatformBundle\Form\AdvertSkillType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AdvertType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $pattern = '%';
        $builder
          ->add('date', DateTimeType::class)
          ->add('title', TextType::class)
          ->add('author', TextType::class)
          ->add('content', CkeditorType::class)
          ->add('published', CheckboxType::class, ['required' => false])
          ->add('image', ImageType::class, ['required' => false])
          /*->add('categories', CollectionType::class, array(
            'entry_type' => CategoryType::class,
            'allow_add' => true,
            'allow_delete' => true
          ))*/
          ->add('categories', EntityType::class, array(
            'class' => 'OCPlatformBundle:Category',
            'choice_label' => 'name',
            'multiple' => true,
            #'expanded' => false,
            #'query_builder' => function(CategoryRepository $repository) use ($pattern) {
            #  return $repository->getLikeQueryBuilder($pattern);
            #}
          ))
          #->add('advertSkills', AdvertSkillType::class)
          ->add('advertSkills', EntityType::class, array(
            'class' => 'OCPlatformBundle:AdvertSkill',
            'choice_label' => 'id',
            #'choice_label' => function ()
            'multiple' => true,
            #'query_builder' => function(AdvertSkillRepository $repository) {
            #  return $repository->createQueryBuilder('ask')
            #  ;
            #}
          ))
          /*->add('advertSkills', CollectionType::class, array(
            'entry_type' => AdvertSkillType::class,
            'allow_add' => true,
            'allow_delete' => true,
          ))*/
          ->add('save', SubmitType::class)
        ;

        $builder->addEventListener(
          FormEvents::PRE_SET_DATA,
          function(FormEvent $event) {
            $advert = $event->getData();
            if(null === $advert) {
              return;
            }

            if(!$advert->getPublished() || null === $advert->getId()) {
              $event->getForm()->add('published', CheckboxType::class, array('required' => false));
            } else {
              $event->getForm()->remove('published');
            }
          }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\PlatformBundle\Entity\Advert'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oc_platformbundle_advert';
    }


}
