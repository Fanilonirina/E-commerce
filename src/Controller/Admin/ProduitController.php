<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use App\Form\ProduitFormType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProduitController extends AbstractController
{
    #[Route('/admin/produit/add', name: 'add_produit')]
    public function add_produit(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $produit = new Produit;
        $form = $this->createForm(ProduitFormType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('product_image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new Exception('Erreur lors du deplacement de l\'image:' . $e);
                }
            } else {
                $newFilename = 'noimage.jpg';
            }
            $produit->setStatus(1);
            $produit->setImage($newFilename);
            $em->persist($produit);
            $em->flush();
            $this->addFlash('success', 'Produit ajouté avec success!!!');
        }
        return $this->render('admin/produit/add_produit.html.twig', [
            'produitForm' => $form->createView()
        ]);
    }

    #[Route('/admin/produit/show', name: 'show_produit')]
    public function show_produit(ProduitRepository $produit_repository): Response
    {
        return $this->render('admin/produit/list_produit.html.twig', [
            'produits' => $produit_repository->findAll()
        ]);
    }

    #[Route('/admin/produit/delete/{id}', name: 'delete_produit')]
    public function delete_produit($id, EntityManagerInterface $em, ProduitRepository $produit_repository): Response
    {
        $produit = $produit_repository->find($id);
        if ($produit) {

            $em->remove($produit);
            $em->flush();
            $this->addFlash('success', 'Produit supprimé avec success!!!');
            return $this->redirectToRoute('show_produit');
        }
    }

    #[Route('admin/produit/edit/{id}', name: 'edit_produit')]
    public function update_produit(
        Produit $produit,
        EntityManagerInterface $em,
        Request $request,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(ProduitFormType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('product_image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new Exception('Erreur lors du deplacement de l\'image:' . $e);
                }
            } else {
                $newFilename = 'noimage.jpg';
            }
            $produit->setStatus(1);
            $produit->setImage($newFilename);
            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Produit modifié avec success!!!');
            return $this->redirectToRoute('show_produit');
        }
        return $this->render('admin/produit/edit_produit.html.twig', [
            'produitForm' => $form->createView()
        ]);
    }

    #[Route('admin/produit/desactiver/{id}', name: 'desactiver_produit')]
    public function desactiver(Produit $produit, EntityManagerInterface $em)
    {
        $produit->setStatus(0);
        $em->persist($produit);
        $em->flush();
        $this->addFlash('success', 'Produit "' . $produit->getDesignation() . '" desactivé !!!');
        return $this->redirectToRoute('show_produit');
    }

    #[Route('admin/produit/activer/{id}', name: 'activer_produit')]
    public function activer(Produit $produit, EntityManagerInterface $em)
    {
        $produit->setStatus(1);
        $em->persist($produit);
        $em->flush();
        $this->addFlash('success', 'Produit "' . $produit->getDesignation() . '" activé !!!');
        return $this->redirectToRoute('show_produit');
    }
}
