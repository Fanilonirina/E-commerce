<?php

namespace App\Controller\Client;

use App\Repository\SliderRepository;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use App\Service\Cart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ClientController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        SliderRepository $slider_repository,
        ProduitRepository $produit_repository
    ): Response {
        return $this->render('client/home.html.twig', [
            'sliders' => $slider_repository->findBy([
                'status' => 1
            ]),
            'produits' => $produit_repository->findBy([
                'status' => 1
            ])
        ]);
    }

    #[Route('/shop', name: 'shop')]
    public function shop(
        CategorieRepository $categorie_repository,
        ProduitRepository $produit_repository
    ): Response {
        return $this->render('client/shop.html.twig', [
            'categories' => $categorie_repository->findAll(),
            'produits' => $produit_repository->findBy([
                'status' => 1
            ])
        ]);
    }

    #[Route('/par_categorie/{id}', name: 'par_categorie')]
    public function par_categorie(
        CategorieRepository $categorie_repository,
        ProduitRepository $produit_repository,
        $id
    ): Response {

        return $this->render('client/shop.html.twig', [
            'categories' => $categorie_repository->findAll(),
            'produits' => $produit_repository->findBy([
                'categorie' => $id,
                'status' => 1
            ])
        ]);
    }

    #[Route('/ajouter_pannier/{id}', name: 'ajouter_pannier')]
    public function add_cart(
        ProduitRepository $produit_repository,
        SessionInterface $session,
        $id
    ): Response {
        $produit = $produit_repository->find($id);
        $oldCart = $session->has('cart') ? $session->get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->add($produit, $id);
        $session->set('cart', $cart);

        return $this->redirectToRoute('shop');
    }

    #[Route('/cart', name: 'cart')]
    public function cart(
        SessionInterface $session
    ): Response {
        if (!$session->has('cart')) return $this->render('client/cart.html.twig');

        $oldCart = $session->has('cart') ? $session->get('cart') : null;
        $cart = new Cart($oldCart);

        return $this->render('client/cart.html.twig', [
            'produits' => $cart->items
        ]);
    }

    #[Route('/modify_qty/{id}', name: 'modify_qty')]
    public function modify_cart(
        SessionInterface $session,
        Request $request,
        $id
    ): Response {
        $oldCart = $session->has('cart') ? $session->get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->updateQty($id, $request->get('quantity'));
        $session->set('cart', $cart);

        return $this->redirectToRoute('cart');
    }

    #[Route('/remove_cart/{id}', name: 'remove_cart')]
    public function remove_cart(
        SessionInterface $session,
        $id
    ): Response {
        $oldCart = $session->has('cart') ? $session->get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->removeItem($id);

        if (count($cart->items) > 0) {
            $session->set('cart', $cart);
        } else {
            $session->remove('cart');
        }

        return $this->redirectToRoute('cart');
    }
}
