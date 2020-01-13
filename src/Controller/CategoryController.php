<?php

namespace App\Controller;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Length;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index()
    {   
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories,
        ]);
    }
    /**
     * @Route("/category/new", name="new")
     */
    public function new(Request $request, ValidatorInterface $validator )
    {
        // createFormBuilder is a shortcut to get the "form factory"
        // and then call "createBuilder()" on it
        $form = $this->createFormBuilder()
            ->add('code', TextType::class,  ['constraints'=>[ new NotBlank() /*, new Regex(array('pattern'=>"[A-Za-z1-9]",'match'=>true,'message'=>'No pueden haber caracteres especiales'))*/],'label' => 'Codigo', 'attr' => ['class'=>'form-control']])
            ->add('name', TextType::class, ['constraints'=>[new Length(['min' => 2, 'minMessage'=> 'Minimo 2 caracteres.'])],'label' => 'Nombre', 'attr' => ['class'=>'form-control']])
            ->add('description', TextType::class, ['label' => 'Descripcion', 'attr' => ['class'=>'form-control']])
            ->add('active', CheckboxType::class, ['label' => 'Activo','required'=>false, 'attr' => ['class'=>'']])
            ->add('Guardar', SubmitType::class, ['label' => 'Guardar', 'attr' => ['class'=>'btn btn-primary']])
            ->getForm();

        if($request->isMethod('POST'))
        {
            $form->submit($request->request->get($form->getName()));
            
            // $form->get('name')->addError($error);
            if($form->isSubmitted() && $form->isValid())
            {

                $entityManager = $this->getDoctrine()->getManager();
                $data = $form->getData();
                $category = new Category();
                $category->setName($data['name']);
                $category->setCode($data['code']);
                $category->setDescription($data['description']);
                $category->setActive($data['active']);
                $error = $validator->validate($category);
                if(count($error))
                {
                    foreach($error as $e)
                    {
                        $form->get($e->getPropertyPath())->addError(new FormError($e->getConstraint()->message));
                    }
                    return $this->render('category/new.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
                $entityManager->persist($category);
                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();
                return $this->redirectToRoute('category');
            }
        }
        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/category/{id}/edit", name="categoryEdit")
     */
    public function update(Category $category, Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $form = $this->createFormBuilder($category)
            ->add('code', TextType::class,  ['constraints'=>[ new NotBlank() /*, new Regex(array('pattern'=>"[A-Za-z1-9]",'match'=>true,'message'=>'No pueden haber caracteres especiales'))*/],'label' => 'Codigo', 'attr' => ['class'=>'form-control']])
            ->add('name', TextType::class, ['constraints'=>[new Length(['min' => 2])],'label' => 'Nombre', 'attr' => ['class'=>'form-control']])
            ->add('description', TextType::class, ['label' => 'Descripcion', 'attr' => ['class'=>'form-control']])
            ->add('active', CheckboxType::class, ['label' => 'Activo','required'=>false, 'attr' => ['class'=>'']])
            ->add('Guardar', SubmitType::class, ['label' => 'Guardar', 'attr' => ['class'=>'btn btn-primary']])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Category $category */
            $category = $form->getData();
            $error = $validator->validate($category);
            if(count($error))
            {
                foreach($error as $e)
                {
                    $form->get($e->getPropertyPath())->addError(new FormError($e->getConstraint()->message));
                }
                return $this->render('category/update.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Actualizado');
            return $this->redirectToRoute('category');
        }
        return $this->render('category/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category/{id}/delete", name="categoryDeleted")
     */
    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $entityManager->remove($category);
        $entityManager->flush();
        return $this->redirectToRoute('category');
    }
}
