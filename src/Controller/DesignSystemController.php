<?php

namespace App\Controller;

use App\Entity\Statut;
use App\Form\StatutFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DesignSystemController extends AbstractController
{
    #[Route('/ds', name: 'app_design_system')]
    public function index(): Response
    {
        return $this->render('design_system/index.html.twig', []);
    }

    #[Route('/ds/form', name: 'app_design_system_form')]
    public function form(Request $request): Response
    {
        $statut = new Statut(); // Assurez-vous d'importer la classe Statut
        $form = $this->createForm(StatutFormType::class, $statut);

        $form->handleRequest($request);
        // dump($form->getData());
        // dump($form->isSubmitted());
        // dump($form->isValid());
        // dd($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement du formulaire (par exemple, enregistrer les données dans la base)
            $this->addFlash('success', 'Formulaire soumis avec succès !');
        }

        return $this->render('design_system/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
