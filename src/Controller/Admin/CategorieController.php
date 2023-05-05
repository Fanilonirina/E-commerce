<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Form\CategorieFormType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    #[Route('/admin/categorie/add', name: 'add_categorie')]
    public function add_categorie(Request $request, EntityManagerInterface $em): Response
    {

        $categorie = new Categorie();
        $form = $this->createForm(CategorieFormType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorie);
            $em->flush();

            $this->addFlash('success', 'Categorie ajouté avec success!!!');
        }
        return $this->render('admin/categorie/add_categorie.html.twig', [
            'categorieForm' => $form->createView()
        ]);
    }

    #[Route('/admin/categorie/list', name: 'show_categorie')]
    public function show_categorie(CategorieRepository $categorie_repository): Response
    {
        $categories = $categorie_repository->findAll();
        return $this->render('admin/categorie/list_categorie.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/admin/categorie/delete/{id_cat}', name: 'delete_categorie')]
    public function delete_categorie($id_cat, EntityManagerInterface $em, CategorieRepository $categorieRepository): Response
    {
        $categorie = $categorieRepository->find($id_cat);
        if ($categorie) {

            $em->remove($categorie);
            $em->flush();
            $this->addFlash('success', 'Categorie supprimé avec success!!!');
            return $this->redirectToRoute('show_categorie');
        }
    }

    #[Route('admin/categorie/edit/{id}', name: 'edit_categorie')]
    public function update_categorie(Categorie $categorie, EntityManagerInterface $em, Request $request): Response
    {
        // $categorie = $categorieRepository->find($id_cat);
        $form = $this->createForm(CategorieFormType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorie);
            $em->flush();

            $this->addFlash('success', 'Categorie modifié avec success!!!');
            return $this->redirectToRoute('show_categorie');
        }
        return $this->render('admin/categorie/edit_categorie.html.twig', [
            'categorieForm' => $form->createView()
        ]);
    }
}
