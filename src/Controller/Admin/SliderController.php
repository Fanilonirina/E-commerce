<?php

namespace App\Controller\Admin;

use Exception;
use App\Entity\Slider;
use App\Form\SliderFormType;
use App\Repository\SliderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class SliderController extends AbstractController
{
    #[Route('/admin/slider/add', name: 'add_slider')]
    public function add_slider(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $slider = new Slider;
        $form = $this->createForm(SliderFormType::class, $slider);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('slider_image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new Exception('Erreur lors du deplacement de l\'image:' . $e);
                }
            } else {
                $newFilename = 'noimage.jpg';
            }
            $slider->setStatus(1);
            $slider->setImage($newFilename);
            $em->persist($slider);
            $em->flush();
            $this->addFlash('success', 'Slider ajouté avec success!!!');
        }
        return $this->render('admin/slider/add_slider.html.twig', [
            'sliderForm' => $form->createView()
        ]);
    }

    #[Route('/admin/slider/show', name: 'show_slider')]
    public function show_slider(SliderRepository $slider_repository): Response
    {
        return $this->render('admin/slider/list_slider.html.twig', [
            'sliders' => $slider_repository->findAll()
        ]);
    }

    #[Route('/admin/slider/delete/{id}', name: 'delete_slider')]
    public function delete_slider($id, EntityManagerInterface $em, SliderRepository $slider_repository): Response
    {
        $slider = $slider_repository->find($id);
        if ($slider) {
            $em->remove($slider);
            $em->flush();
            $this->addFlash('success', 'Slider supprimé avec success!!!');
            return $this->redirectToRoute('show_slider');
        }
    }

    #[Route('admin/slider/edit/{id}', name: 'edit_slider')]
    public function update_slider(
        Slider $slider,
        EntityManagerInterface $em,
        Request $request,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(SliderFormType::class, $slider);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('slider_image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new Exception('Erreur lors du deplacement de l\'image:' . $e);
                }
            } else {
                $newFilename = 'noimage.jpg';
            }
            $slider->setStatus(1);
            $slider->setImage($newFilename);
            $em->persist($slider);
            $em->flush();

            $this->addFlash('success', 'Slider modifié avec success!!!');
            return $this->redirectToRoute('show_slider');
        }
        return $this->render('admin/slider/edit_slider.html.twig', [
            'sliderForm' => $form->createView()
        ]);
    }

    #[Route('admin/slider/desactiver/{id}', name: 'desactiver_slider')]
    public function desactiver(Slider $slider, EntityManagerInterface $em)
    {
        $slider->setStatus(0);
        $em->persist($slider);
        $em->flush();
        $this->addFlash('success', 'Slider desactivé !!!');
        return $this->redirectToRoute('show_slider');
    }

    #[Route('admin/slider/activer/{id}', name: 'activer_slider')]
    public function activer(slider $slider, EntityManagerInterface $em)
    {
        $slider->setStatus(1);
        $em->persist($slider);
        $em->flush();
        $this->addFlash('success', 'Slider activé !!!');
        return $this->redirectToRoute('show_slider');
    }
}
