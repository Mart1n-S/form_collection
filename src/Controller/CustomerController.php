<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Association;
use App\Form\AssociationFormType;
use App\Form\CustomerFormType;
use App\Model\CustomerFormModel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomerController extends AbstractController
{

    #[Route('/form-customer', name: 'app_customer')]
    public function test(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une instance du modèle
        $customer = new Customer();
        // Créer le formulaire
        $form = $this->createForm(CustomerFormType::class, $customer);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $customer = $form->getData();

            // Enregistrer le client en base de données
            $entityManager->persist($customer);
            $entityManager->flush();

            dd('Client enregistré');
        }

        // Afficher le formulaire dans le template Twig
        return $this->render('customer/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/form-customer2', name: 'app_customer_v2')]
    public function testModel(Request $request, EntityManagerInterface $entityManager): Response
    {
        $association = new Association();
        // Créer le formulaire
        $form = $this->createForm(AssociationFormType::class, $association);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $formData = $form->getData();

            dd($formData);
        }

        // Afficher le formulaire dans le template Twig
        return $this->render('customer/form2.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
