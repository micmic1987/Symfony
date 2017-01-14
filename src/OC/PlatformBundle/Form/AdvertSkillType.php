<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use OC\PlatformBundle\OCPlatformBundle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use OC\PlatformBundle\Repository\SkillRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdvertSkillType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          #->add('level', TextType::class)
          #->add('advert', AdvertType::class)
          /*->add('skill', EntityType::class, array(
            'class' => 'OCPlatformBundle:Skill',
            #'choice_label' => 'skill',
            #'multiple' => true,
            'query_builder' => function(SkillRepository $repository) {
              return $repository->createQueryBuilder('s');
            }
          ))*/
          /*->add('skill', ChoiceType::class, array(
            'choices' => array(1,2,3)
          ))*/
          ->add('skill', SkillType::class)
          /*->add('skill', EntityType::class, array(
            'class' => 'OCPlatformBundle:Skill',
            'choice_label' => 'name',
            'multiple' => true,
          ))*/
        /*->add('skill', CollectionType::class, array(
          'entry_type' => SkillType::class,
          'allow_add' => true,
          'allow_delete' => true,
        ))*/
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\PlatformBundle\Entity\AdvertSkill'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oc_platformbundle_advertskill';
    }


}
