<?php

namespace App\Controller;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $products = $entityManager
            ->getRepository('App\Entity\Product')
            ->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->where('c.active = 1')
            ->getQuery()
            ->getResult();
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $products,
        ]);
    }

    /**
     * @Route("/product/new", name="productNew")
     */
    public function new(Request $request, ValidatorInterface $validator )
    {
        // createFormBuilder is a shortcut to get the "form factory"
        // and then call "createBuilder()" on it
        $form = $this->createFormBuilder()
            ->add('code', TextType::class,  ['constraints'=>[ new NotBlank()],'label' => 'Codigo', 'attr' => ['class'=>'form-control']])
            ->add('name', TextType::class, ['constraints'=>[new Length(['min' => 2, 'minMessage'=> 'Minimo 2 caracteres.'])],'label' => 'Nombre', 'attr' => ['class'=>'form-control']])
            ->add('description', TextType::class, ['label' => 'Descripcion', 'attr' => ['class'=>'form-control']])
            ->add('make', TextType::class, ['label' => 'Marca','required'=>false, 'attr' => ['class'=>'form-control']])
            ->add('price', TextType::class, ['label' => 'Precio','required'=>false, 'attr' => ['class'=>'form-control']])
            ->add('category', EntityType::class, ['class' => 'App\Entity\Category', 'placeholder' => 'Seleccione categoria','query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where('u.active = 1');
            }, 'attr' => ['class'=>'form-control']])
            ->add('Guardar', SubmitType::class, [ 'label' => 'Guardar', 'attr' => ['class'=>'btn btn-primary']])
            ->getForm();

        if($request->isMethod('POST'))
        {
            $form->submit($request->request->get($form->getName()));
            if($form->isSubmitted() && $form->isValid())
            {
                $entityManager = $this->getDoctrine()->getManager();
                $data = $form->getData();
                $product = new Product();
                $product->setName($data['name']);
                $product->setCode($data['code']);
                $product->setDescription($data['description']);
                $product->setPrice($data['price']); 
                $product->setCategory($data['category']); 
                $product->setMake($data['make']); 
                $error = $validator->validate($product);
                if(count($error))
                {
                    foreach($error as $e)
                    {
                        $form->get($e->getPropertyPath())->addError(new FormError($e->getConstraint()->message));
                    }
                    return $this->render('product/new.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
                $entityManager->persist($product);
                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();
                return $this->redirectToRoute('product');
            }
        }
        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
     /**
     * @Route("/product/{id}/edit", name="productEdit")
     */
    public function update(Product $product, Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $form = $this->createFormBuilder($product)
            ->add('code', TextType::class,  ['constraints'=>[ new NotBlank()],'label' => 'Codigo', 'attr' => ['class'=>'form-control']])
            ->add('name', TextType::class, ['constraints'=>[new Length(['min' => 2, 'minMessage'=> 'Minimo 2 caracteres.'])],'label' => 'Nombre', 'attr' => ['class'=>'form-control']])
            ->add('description', TextType::class, ['label' => 'Descripcion', 'attr' => ['class'=>'form-control']])
            ->add('make', TextType::class, ['label' => 'Marca','required'=>false, 'attr' => ['class'=>'form-control']])
            ->add('price', TextType::class, ['label' => 'Precio','required'=>false, 'attr' => ['class'=>'form-control']])
            ->add('category', EntityType::class, ['class' => 'App\Entity\Category', 'placeholder' => 'Seleccione categoria','query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where('u.active = 1');
            }, 'attr' => ['class'=>'form-control']])
            ->add('Guardar', SubmitType::class, [ 'label' => 'Guardar', 'attr' => ['class'=>'btn btn-primary']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Product $product */
            $product = $form->getData();
            $error = $validator->validate($product);
            if(count($error))
            {
                foreach($error as $e)
                {
                    $form->get($e->getPropertyPath())->addError(new FormError($e->getConstraint()->message));
                }
                return $this->render('product/update.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Actualizado');
            return $this->redirectToRoute('product');
        }
        return $this->render('product/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/product/{id}/delete", name="productDeleted")
     */
    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $entityManager->remove($product);
        $entityManager->flush();
        return $this->redirectToRoute('product');
    }
}
