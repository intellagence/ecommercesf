<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Material;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Count;

class ProductType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('title', null, [
                'label' => 'Titre*', // ou false pour ne pas l'avoir
                'required' => false,
                'label_attr' => [
                    'class' => 'text-primary'
                ],
                'attr' => [
                    'placeholder' => 'Saisir le titre du produit',
                ],
                'help' => 'Saisir le titre du produit (5-25 caractères)',
                'help_attr' => [
                    'class' => 'fst-italic text-info'
                ],
                'row_attr' => [
                    'class' => 'border border-danger m-2 p-2'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le titre du produit'
                    ]),
                    new Length([
                        'min' => 5,
                        'max' => 25,
                        'minMessage' => 'Veuillez saisir au moins 5 caractères',
                        'maxMessage' => 'Veuillez saisir au maximum 25 caractères'
                    ])
                ]
            ])

            ->add('price', MoneyType::class, [
                // 'currency' => 'USD'
                'label' => 'Prix*', // ou false pour ne pas l'avoir
                'required' => false,
                'label_attr' => [
                    'class' => 'text-primary'
                ],
                'attr' => [
                    'placeholder' => 'Saisir le prix du produit',
                ],
                'help' => 'Saisir le prix du produit (> 0)',
                'help_attr' => [
                    'class' => 'fst-italic text-info'
                ],
                'row_attr' => [
                    'class' => 'border border-danger m-2 p-2'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le prix du produit'
                    ]),
                    new Positive([
                        'message' => 'Veuillez saisir un nombre strictement supérieur à zéro'
                    ])
                ]
            ])

            ->add('description', null, [
                'label' => 'Description (facultatif)', // ou false pour ne pas l'avoir
                'required' => false,
                'label_attr' => [
                    'class' => 'text-primary'
                ],
                'attr' => [
                    'placeholder' => 'Saisir la description du produit',
                    'rows' => 4
                ],
                'help' => 'Saisir la description du produit (200 caractères max)',
                'help_attr' => [
                    'class' => 'fst-italic text-info'
                ],
                'row_attr' => [
                    'class' => 'border border-danger m-2 p-2'
                ],
                'constraints' => [
                    new Length([
                        'max' => 200,
                        'maxMessage' => 'Veuillez saisir moins de 200 caractères'
                    ])
                ]
            ])

            ->add('category', EntityType::class, [ // propriété relationnelle
                'class' => Category::class, // nom de la class Entity en relation
                'choice_label' => 'name', // nom de la propriété des objets Category
                // 'choice_label' => function(Category $category){
                //     return $category->getId() . ' ' . $category->getName();
                // }
                //'expanded' => true, changement des balises HTML select -> radio/checkbox
                'label' => 'Catégorie*',
                'placeholder' => 'Saisir une catégorie',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une catégorie'
                    ])
                ]
            ])

            ->add('brand', EntityType::class, [
                'class' => Brand::class,
                'choice_label' => 'name',
                'label' => 'Marque*',
                'placeholder' => 'Saisir une marque',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une marque'
                    ])
                ]
            ])

            ->add('materials', EntityType::class, [
                'class' => Material::class,
                'choice_label' => 'name',
                'multiple' => true, // pour les relations ManytoMany
                'expanded' => true,
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'Veuillez sélectionner au moins une matière'
                    ])
                ]
            ])

            // ->add('Ajouter', SubmitType::class)
        ;

        /*
            L'objet builder est la construction du formulaire
            C'est ici qu'on va configurer/paramètrer le contenu du formulaire

            Un formulaire peut être rattaché ou non à une entity
            Si c'est le cas, les éléments du formulaire doivent correspondre aux propriétés du formulaire

            Chaque élément (= children) du formulaire est placé dans une méthode add()

            méthode add() 
                - 1e argument (string) : nom de la propriété 
                - 2e argument (class) : définir le type de l'élément du formulaire
                - 3e argument (array) : tableau des options
                                        Il y a 2 "types" d'options :
                                            - options communes (inherited options)
                                            - options propres (field options)

        */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
