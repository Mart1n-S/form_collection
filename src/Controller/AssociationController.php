<?php

namespace App\Controller;

use App\Entity\Association;
use App\Form\AssociationType;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AssociationController extends AbstractController
{
    #[Route('/association', name: 'app_association')]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        // Créer une nouvelle instance d'Association
        $association = new Association();

        // Créer le formulaire
        $form = $this->createForm(AssociationType::class, $association);

        // Traitement de la requête
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            foreach ($data->getMembres() as $membre) {
                // Accéder aux documents de chaque membre
                $documentsOfMembre = $membre->getDocuments(); // Récupérer les documents

                // Logique pour traiter les documents, par exemple, les ajouter à un tableau pour un email
                foreach ($documentsOfMembre as $document) {
                    $numeroSiret = $document['numeroSiret'];
                    $chiffreAffaires = $document['chiffreAffaires'];

                    $email = (new Email())
                        ->from('votre_email@example.com')
                        ->to('destinataire@example.com')
                        ->subject('Nouveaux membres et documents')
                        ->html($this->renderView('emails/membre_notification.html.twig', [
                            'numeroSiret' => $numeroSiret,
                            'chiffreAffaires' => $chiffreAffaires,
                        ]));

                    // Envoyer l'e-mail
                    $mailer->send($email);
                }
            }

            // Persister l'association et les membres
            foreach ($association->getMembres() as $membre) {

                $membre->setAssociation($association);
                $entityManager->persist($membre);
            }

            $entityManager->persist($association);
            $entityManager->flush();

            // Redirection après succès
            return $this->redirectToRoute('association_success');
        }

        return $this->render('association/form_association.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/association/success', name: 'association_success')]
    public function success(): Response
    {
        return $this->render('association/association_success.html.twig');
    }
}
