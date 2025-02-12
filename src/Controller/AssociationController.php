<?php

namespace App\Controller;

use App\Form\TestFormType;
use App\Entity\Association;
use App\Entity\DowloadToken;
use Psr\Log\LoggerInterface;
use App\Form\AssociationType;
use Symfony\Component\Mime\Email;
use App\Service\VerificationService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use App\Repository\DowloadTokenRepository;
use App\Service\MailService;
use App\Service\PdfGeneratorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AssociationController extends AbstractController
{
    public function __construct(private VerificationService $checkEnvironment, private LoggerInterface $loggerInterface) {}

    #[Route('/test', name: 'app_test')]
    public function test(Request $request, PdfGeneratorService $pdfGenerator, MailService $mailService): Response
    {
        $form = $this->createForm(TestFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // 1. Générer le HTML du PDF
            $htmlContent = $this->renderView('pdf/test_pdf.html.twig', [
                'data' => $data,
            ]);

            // 2. Générer le PDF
            $pdfContent = $pdfGenerator->generatePdf($htmlContent);

            // 3. Envoyer le PDF par email
            $mailService->sendPdfByEmail('destinataire@example.com', $pdfContent, 'test.pdf');

            // 4. Redirection après envoi
            return $this->redirectToRoute('app_success');
        }
        // Ajouter un flash message en cas d'erreur
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('danger', 'Votre formulaire contient des erreurs.');
        }

        return $this->render('association/test2.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/success-mail', name: 'app_success')]
    public function success2(): Response
    {
        return $this->render('association/success.html.twig');
    }

    #[Route('/association', name: 'app_association')]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        // Vérifier l'environnement
        $environment = $this->checkEnvironment->verifySomething();
        // dump($environment);
        // Créer une nouvelle instance d'Association
        $association = new Association();

        // Créer le formulaire
        $form = $this->createForm(AssociationType::class, $association);

        // Traitement de la requête
        $form->handleRequest($request);
        // dd($form->getData());
        if ($form->isSubmitted() && $form->isValid()) {

            // Générer un token unique
            $token = bin2hex(random_bytes(16)); // 32 caractères hexadécimaux

            $data = $form->getData();

            // Créer un dossier unique pour cette soumission (par exemple basé sur un identifiant unique)
            $uniqueFolder = md5(uniqid());
            $uploadDirectory = $this->getParameter('uploads_directory') . '/' . $uniqueFolder;

            // Créer le répertoire si ce n'est pas déjà fait
            if (!file_exists($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }
            // <--------Simplification du code on zip directement et on enregistre le zip plutot que de l'enregistrer puis zipperpouis supprime les fichier non zipper-------->
            // Création d'une archive ZIP directement
            $zip = new \ZipArchive();
            $zipFileName = $uploadDirectory . '/documents.zip';

            if ($zip->open($zipFileName, \ZipArchive::CREATE) === TRUE) {

                foreach ($data->getMembres() as $membre) {
                    // Accéder aux documents de chaque membre
                    $cni = $membre->getCni();
                    $justificatifDomicile = $membre->getJustificatifDomicile();

                    if ($cni) {
                        // Générer un nom de fichier unique et l'ajouter directement au ZIP
                        $cniFileName = md5(uniqid()) . '.' . $cni->guessExtension();
                        $zip->addFromString($cniFileName, file_get_contents($cni->getPathname()));
                        $zip->setCompressionIndex($zip->numFiles - 1, \ZipArchive::CM_DEFLATE); // Compression maximale
                    }

                    if ($justificatifDomicile) {
                        // Générer un nom de fichier unique et l'ajouter directement au ZIP
                        $justificatifDomicileFileName = md5(uniqid()) . '.' . $justificatifDomicile->guessExtension();
                        $zip->addFromString($justificatifDomicileFileName, file_get_contents($justificatifDomicile->getPathname()));
                        $zip->setCompressionIndex($zip->numFiles - 1, \ZipArchive::CM_DEFLATE); // Compression maximale
                    }

                    // Associer le membre à l'association
                    $membre->setAssociation($association);
                    $entityManager->persist($membre);
                }

                // Fermer le fichier ZIP
                $zip->close();
            } else {
                foreach ($data->getMembres() as $membre) {
                    // Récupérer les fichiers CNI et justificatif de domicile du membre
                    $cni = $membre->getCni();
                    $justificatifDomicile = $membre->getJustificatifDomicile();

                    // Copier le fichier CNI dans le dossier de téléchargement principal, s'il existe
                    if ($cni) {
                        $cniFileName = md5(uniqid()) . '.' . $cni->guessExtension();
                        $cni->move($uploadDirectory, $cniFileName);
                    }

                    // Copier le fichier justificatif de domicile dans le dossier de téléchargement principal, s'il existe
                    if ($justificatifDomicile) {
                        $justificatifDomicileFileName = md5(uniqid()) . '.' . $justificatifDomicile->guessExtension();
                        $justificatifDomicile->move($uploadDirectory, $justificatifDomicileFileName);
                    }

                    // Associer le membre à l'association
                    $membre->setAssociation($association);
                    $entityManager->persist($membre);
                }
            }


            // // Tableau pour stocker les chemins des fichiers
            // $files = [];

            // foreach ($data->getMembres() as $membre) {
            //     // Accéder aux documents de chaque membre
            //     $cni = $membre->getCni();
            //     $justificatifDomicile = $membre->getJustificatifDomicile();

            //     if ($cni) {
            //         // Générer un nom de fichier unique pour chaque document
            //         $cniFileName = md5(uniqid()) . '.' . $cni->guessExtension();
            //         // Sauvegarder le document CNI dans le répertoire unique
            //         $cni->move($uploadDirectory, $cniFileName);
            //         // Ajouter le chemin au tableau de fichiers
            //         $files[] = $uploadDirectory . '/' . $cniFileName;
            //     }

            //     if ($justificatifDomicile) {
            //         // Générer un nom de fichier unique pour chaque document
            //         $justificatifDomicileFileName = md5(uniqid()) . '.' . $justificatifDomicile->guessExtension();
            //         // Sauvegarder le justificatif de domicile dans le répertoire unique
            //         $justificatifDomicile->move($uploadDirectory, $justificatifDomicileFileName);
            //         // Ajouter le chemin au tableau de fichiers
            //         $files[] = $uploadDirectory . '/' . $justificatifDomicileFileName;
            //     }

            //     // Associer le membre à l'association
            //     $membre->setAssociation($association);
            //     $entityManager->persist($membre);
            // }

            // // Création d'une archive ZIP contenant tous les fichiers
            // $zip = new \ZipArchive();
            // $zipFileName = $uploadDirectory . '/documents.zip';

            // if ($zip->open($zipFileName, \ZipArchive::CREATE) === TRUE) {
            //     // Sans compression
            //     // foreach ($files as $file) {
            //     //     // Ajouter chaque fichier au ZIP
            //     //     $zip->addFile($file, basename($file));
            //     // }

            //     // Avec compression
            //     foreach ($files as $file) {
            //         // Ajouter chaque fichier au ZIP
            //         $zip->addFile($file, basename($file));
            //         // Ajuster le niveau de compression pour chaque fichier (9 = compression maximale)
            //         $zip->setCompressionIndex($zip->numFiles - 1, \ZipArchive::CM_DEFLATE);
            //     }
            //     // Fermer le fichier ZIP
            //     $zip->close();

            //     // Suppression des fichiers après avoir créé le ZIP
            //     foreach ($files as $file) {
            //         if (file_exists($file)) {
            //             unlink($file); // Supprime le fichier
            //         }
            //     }
            // } else {
            //     throw new \Exception('Impossible de créer le fichier ZIP');
            // }

            // Persister l'association et les membres
            $entityManager->persist($association);
            $entityManager->flush();

            // Enregistrer le token et le chemin dans la base de données
            $downloadToken = new DowloadToken();
            $downloadToken->setToken($token);
            $downloadToken->setFolderPath($uniqueFolder);

            $entityManager->persist($downloadToken);
            $entityManager->flush();


            // Générer un lien pour télécharger le fichier ZIP avec le token
            $downloadLink = $this->generateUrl('association_download', [
                'token' => $token
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            // Envoyer l'email avec le lien de téléchargement
            $email = (new Email())
                ->from('noreply@yourdomain.com')
                ->to($association->getEmail())
                ->subject('Téléchargez les documents de votre association')
                ->html('<p>Bonjour,</p><p>Merci d\'avoir soumis les informations de votre association. Vous pouvez télécharger les documents en utilisant le lien suivant : <a href="' . $downloadLink . '">Télécharger les documents</a>.</p>');

            $mailer->send($email);

            // Lien de téléchargement direct
            // // Générer un lien pour télécharger le fichier ZIP
            // $downloadLink = $this->generateUrl('association_download_zip', [
            //     'folder' => $uniqueFolder
            // ], UrlGeneratorInterface::ABSOLUTE_URL);


            // // Envoyer l'email avec le lien de téléchargement
            // $email = (new Email())
            //     ->from('noreply@yourdomain.com')
            //     ->to($association->getEmail())
            //     ->subject('Téléchargez les documents de votre association')
            //     ->html('<p>Bonjour,</p><p>Merci d\'avoir soumis les informations de votre association. Vous pouvez télécharger les documents en utilisant le lien suivant : <a href="' . $downloadLink . '">Télécharger les documents</a>.</p>');

            // $mailer->send($email);


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




    // Méthode pour supprimer un répertoire et son contenu
    private function deleteDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $filePath = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($filePath)) {
                $this->deleteDirectory($filePath);
            } else {
                unlink($filePath); // Supprimer chaque fichier
            }
        }

        return rmdir($dir); // Supprimer le répertoire après son contenu
    }


    // #[Route('/association/download/{token}', name: 'association_download')]
    // public function downloadByToken(string $token, DowloadTokenRepository $dowloadTokenRepository): Response
    // {
    //     // Rechercher le DownloadToken en fonction du token
    //     $downloadToken = $dowloadTokenRepository->findOneBy(['token' => $token]);

    //     if (!$downloadToken) {
    //         throw $this->createNotFoundException('Token invalide.');
    //     }

    //     // Récupérer le chemin du dossier
    //     $folderPath = $downloadToken->getFolderPath();
    //     $zipFile = $this->getParameter('uploads_directory') . '/' . $folderPath . '/documents.zip';

    //     // Vérifier si le fichier ZIP existe
    //     if (!file_exists($zipFile)) {
    //         throw $this->createNotFoundException('Le fichier ZIP n\'existe pas.');
    //     }

    //     return $this->render('association/download.html.twig', [
    //         'downloadLink' => $this->generateUrl('association_download_zip', ['folder' => $folderPath]),
    //         'token' => $token, // Passer le token si nécessaire pour une future utilisation
    //     ]);
    // }

    #[Route('/association/download/{token}', name: 'association_download')]
    public function downloadByToken(string $token, DowloadTokenRepository $dowloadTokenRepository): Response
    {
        // Rechercher le DownloadToken en fonction du token
        $downloadToken = $dowloadTokenRepository->findOneBy(['token' => $token]);

        if (!$downloadToken) {
            throw $this->createNotFoundException('Token invalide.');
        }

        // Récupérer le chemin du dossier principal
        $folderPath = $downloadToken->getFolderPath();
        $zipFile = $this->getParameter('uploads_directory') . '/' . $folderPath . '/documents.zip';


        // Vérifier si le fichier ZIP existe
        if (file_exists($zipFile)) {
            return $this->render('association/download.html.twig', [
                'downloadLink' => $this->generateUrl('association_download_zip', ['folder' => $downloadToken->getFolderPath()]),
                'token' => $token,
            ]);
        }

        // Si le fichier ZIP n'existe pas, récupérer les fichiers individuels
        $files = [];
        foreach (scandir($this->getParameter('uploads_directory') . '/' . $folderPath) as $file) {
            if ($file !== '.' && $file !== '..') {
                $files[] = [
                    'name' => $file,
                ];
            }
        }

        // Si aucun fichier n'est trouvé
        if (empty($files)) {
            throw $this->createNotFoundException('Aucun fichier disponible pour téléchargement.');
        }

        return $this->render('association/download.html.twig', [
            'files' => $files,
            'folder' => $folderPath,
        ]);
    }



    #[Route('/association/zip/download/{folder}', name: 'association_download_zip')]
    public function downloadZip(string $folder, DowloadTokenRepository $dowloadTokenRepository): StreamedResponse
    {
        // Rechercher le DownloadToken en fonction du dossier
        $downloadToken = $dowloadTokenRepository->findOneBy(['folderPath' => $folder]);
        if (!$downloadToken) {
            throw $this->createNotFoundException('Token invalide.');
        }

        $uploadDirectory = $this->getParameter('uploads_directory') . '/' . $folder;
        $zipFile = $uploadDirectory . '/documents.zip';

        // Vérifier si le fichier ZIP existe
        if (!file_exists($zipFile)) {
            throw $this->createNotFoundException('Le fichier ZIP n\'existe pas.');
        }

        // Créer une réponse streamée pour s'assurer que le fichier est bien envoyé avant toute suppression
        $response = new StreamedResponse(function () use ($zipFile, $uploadDirectory) {
            // Ouvrir le fichier et envoyer son contenu
            readfile($zipFile);

            // Supprimer le répertoire après l'envoi du fichier
            $this->deleteDirectory($uploadDirectory);
        });

        // Paramètres de la réponse
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'documents.zip'
        ));

        // Supprimer le token après le téléchargement
        $dowloadTokenRepository->remove($downloadToken, true);

        $this->loggerInterface->info('Document téléchargé', [
            'user' => 'B512345',
            'folder' => $folder,
            'timestamp' => new \DateTime(),
        ]);

        return $response;
    }

    // #[Route('/association/file/download/{folder}/{filename}', name: 'association_download_file')]
    // public function downloadFile(string $folder, string $filename, DowloadTokenRepository $dowloadTokenRepository): StreamedResponse
    // {
    //     // Rechercher le DownloadToken en fonction du dossier
    //     $downloadToken = $dowloadTokenRepository->findOneBy(['folderPath' => $folder]);
    //     if (!$downloadToken) {
    //         throw $this->createNotFoundException('Token invalide.');
    //     }

    //     // Chemin vers le répertoire des téléchargements
    //     $uploadDirectory = $this->getParameter('uploads_directory') . '/' . $folder;
    //     $filePath = $uploadDirectory . '/' . $filename;

    //     // Vérifier que le fichier existe
    //     if (!file_exists($filePath)) {
    //         throw $this->createNotFoundException('Fichier non trouvé.');
    //     }

    //     // Créer une réponse StreamedResponse pour envoyer le fichier en streaming
    //     $response = new StreamedResponse(function () use ($filePath) {
    //         // Ouvrir le fichier et envoyer son contenu
    //         $file = fopen($filePath, 'rb');
    //         while (!feof($file)) {
    //             echo fread($file, 1024); // Lire 1 Ko à la fois
    //             flush(); // S'assurer que les données sont envoyées au client
    //         }
    //         fclose($file); // Fermer le fichier après envoi
    //     });

    //     // Définir les en-têtes pour le téléchargement du fichier
    //     $response->headers->set('Content-Type', 'application/octet-stream');
    //     $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
    //         ResponseHeaderBag::DISPOSITION_ATTACHMENT,
    //         $filename
    //     ));

    //     // Enregistrer la fonction de suppression du fichier et du répertoire après le téléchargement
    //     register_shutdown_function(function () use ($filePath, $uploadDirectory, $dowloadTokenRepository, $downloadToken) {
    //         // Supprimer le fichier après qu'il ait été téléchargé
    //         if (file_exists($filePath)) {
    //             unlink($filePath);
    //         }

    //         // Vérifier si le répertoire est vide après la suppression du fichier
    //         $remainingFiles = array_diff(scandir($uploadDirectory), ['.', '..']);
    //         if (empty($remainingFiles)) {
    //             // Si le répertoire est vide, supprimer le répertoire
    //             rmdir($uploadDirectory);
    //             // Supprimer le token après le téléchargement
    //             $dowloadTokenRepository->remove($downloadToken, true);
    //         }
    //     });

    //     // Retourner la réponse de streaming
    //     return $response;
    // }


    // Methode 1 binary
    // #[Route('/association/file/download/{folder}/{filename}', name: 'association_download_file')]
    // public function downloadFile(string $folder, string $filename, DowloadTokenRepository $dowloadTokenRepository): BinaryFileResponse
    // {
    //     // Rechercher le DownloadToken en fonction du dossier
    //     $downloadToken = $dowloadTokenRepository->findOneBy(['folderPath' => $folder]);
    //     if (!$downloadToken) {
    //         throw $this->createNotFoundException('Token invalide.');
    //     }

    //     // Chemin vers le répertoire des téléchargements
    //     $uploadDirectory = $this->getParameter('uploads_directory') . '/' . $folder;
    //     $filePath = $uploadDirectory . '/' . $filename;

    //     // Vérifier que le fichier existe
    //     if (!file_exists($filePath)) {
    //         throw $this->createNotFoundException('Fichier non trouvé.');
    //     }

    //     // Utiliser BinaryFileResponse pour envoyer le fichier
    //     $response = new BinaryFileResponse($filePath);
    //     $response->setContentDisposition(
    //         ResponseHeaderBag::DISPOSITION_ATTACHMENT,
    //         $filename
    //     );

    //     // Enregistrer une fonction de suppression pour le fichier et le répertoire
    //     register_shutdown_function(function () use ($filePath, $uploadDirectory, $dowloadTokenRepository, $downloadToken) {
    //         // Supprimer le fichier après le téléchargement
    //         if (file_exists($filePath)) {
    //             unlink($filePath);
    //         }

    //         // Vérifier si le répertoire est vide après suppression du fichier
    //         $remainingFiles = array_diff(scandir($uploadDirectory), ['.', '..']);
    //         if (empty($remainingFiles)) {
    //             // Si le répertoire est vide, supprimer le répertoire
    //             rmdir($uploadDirectory);
    //             // Supprimer le token associé
    //             $dowloadTokenRepository->remove($downloadToken, true);
    //         }
    //     });

    //     return $response;
    // }

    // Methode 2 binary
    #[Route('/association/file/download/{folder}/{filename}', name: 'association_download_file')]
    public function downloadFile(string $folder, string $filename, DowloadTokenRepository $dowloadTokenRepository): BinaryFileResponse
    {
        $downloadSuccess = false; // Indicateur de succès du téléchargement

        // Rechercher le DownloadToken en fonction du dossier
        $downloadToken = $dowloadTokenRepository->findOneBy(['folderPath' => $folder]);
        if (!$downloadToken) {
            throw $this->createNotFoundException('Token invalide.');
        }

        // Chemin vers le répertoire des téléchargements
        $uploadDirectory = $this->getParameter('uploads_directory') . '/' . $folder;
        $filePath = $uploadDirectory . '/' . $filename;

        // Vérifier que le fichier existe
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Fichier non trouvé.');
        }

        try {
            // Utiliser BinaryFileResponse pour envoyer le fichier
            $response = new BinaryFileResponse($filePath);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $filename
            );

            // Marquer le téléchargement comme réussi avant de retourner la réponse
            $downloadSuccess = true;

            return $response;
        } catch (\Exception $e) {
            // Gestion des erreurs de téléchargement ici si nécessaire
            throw $this->createNotFoundException('Erreur lors du téléchargement.');
        } finally {
            register_shutdown_function(function () use (
                $filePath,
                $uploadDirectory,
                $dowloadTokenRepository,
                $downloadToken,
                &$downloadSuccess
            ) {
                // Ne supprimer le fichier et le répertoire que si le téléchargement a réussi
                if ($downloadSuccess) {
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }

                    $remainingFiles = array_diff(scandir($uploadDirectory), ['.', '..']);
                    if (empty($remainingFiles)) {
                        rmdir($uploadDirectory);
                        $dowloadTokenRepository->remove($downloadToken, true);
                    }
                }
            });
        }
    }

    #[Route('/mailto', name: 'app_mailto')]
    public function mailto(): Response
    {
        return $this->render('association/mailto.html.twig');
    }
}
