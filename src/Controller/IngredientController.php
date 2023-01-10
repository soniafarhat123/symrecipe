<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    /**
     * This function display all ingredients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    public function index(IngredientRepository $repository,  PaginatorInterface $paginator, Request $request): Response
    {
        $ingredient = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredient
        ]);
        /*
        $ingredients = $repository->findAll();
         #dd($ingredients);
         return $this->render('pages/ingredient/index.html.twig', [
             'ingredients' => $ingredients
         ]);
        */
        /* return $this->render('pages/ingredient/index.html.twig', [
             'ingredients' => $repository->findAll()
         ]);*/
    }

    /**
     * This controller show a form which create a new ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/nouveau', 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class,$ingredient);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient= $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

            $this->AddFlash(
                'success',
                'Votre Ingrédient a été crée avec succés !'
            );

            return $this->redirectToRoute('ingredient.index');
        }
        return $this->render('pages/ingredient/new.html.twig',
        [
            'form' => $form->createView()
        ]);
    }


   /* #[Route('ingredient/edition/{id}', name: 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(IngredientRepository $repository, int $id, EntityManagerInterface $manager) : Response
    {
        $ingredient = $repository ->findOneBy(["id" => $id]);
        $form = $this->createForm(IngredientType::class, $ingredient);
        return $this->render('pages/ingredient/edit.html.twig',
        [
            'form' => $form->createView()
        ]);
    }*/

    /**
     * This controller modify an ingredient
     *
     * @param Ingredient $ingredient
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('ingredient/edition/{id}', name: 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(Ingredient $ingredient,Request $request, EntityManagerInterface $manager) : Response
    {
        //dd($ingredient);
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient= $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

            $this->AddFlash(
                'success',
                'Votre Ingrédient a été modifié avec succés !'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/edit.html.twig',
            [
                'form' => $form->createView()
            ]);
    }

    #[Route('/ingredient/suppression/{id}', name: 'ingredient.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient) : Response
    {

        if(!$ingredient){
            $this->AddFlash(
                'success',
                'L\'Ingrédient en question n\'a pas été trouvé !'
            );
            $this->redirectToRoute('ingredient.index');
        }

        $manager->remove($ingredient);
        $manager->flush();

        $this->AddFlash(
            'success',
            'Votre Ingrédient a été supprimé avec succés !'
        );

        return $this->redirectToRoute('ingredient.index');
    }

}
