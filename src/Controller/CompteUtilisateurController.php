<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Trajet;
use App\Entity\Vehicule;
use App\Entity\Preferences;
use App\Form\TypeUtilisateurType;
use App\Form\TrajetForm;
use App\Form\VehiculeFormType;
use App\Form\PreferencesFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CompteUtilisateurController extends AbstractController
{
    #[Route('/utilisateur', name: 'compte_utilisateur')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $typeForm = $this->createForm(TypeUtilisateurType::class, $user);
        $typeForm->handleRequest($request);

        if ($typeForm->isSubmitted() && $typeForm->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('compte_utilisateur'); // <-- important
        }

        $isConducteur = $user->getTypeUtilisateur() === 'conducteur';

        // Initialisation
        $vehiculeFormView = null;
        $preferencesFormView = null;
        $trajetFormView = null;
        $hasVehicule = false;

        if ($isConducteur) {
            $vehiculeForm = $this->handleVehiculeForm($request, $em, $user);
            if ($vehiculeForm instanceof Response) {
                return $vehiculeForm;
            }
            $vehiculeFormView = $vehiculeForm;

            $preferencesForm = $this->handlePreferencesForm($request, $em, $user);
            if ($preferencesForm instanceof Response) {
                return $preferencesForm;
            }
            $preferencesFormView = $preferencesForm;

            [$trajetForm, $hasVehicule] = $this->handleTrajetForm($request, $em, $user);
            if ($trajetForm instanceof Response) {
                return $trajetForm;
            }
            $trajetFormView = $trajetForm;
        }

        return $this->render('compte_utilisateur.html.twig', [
            'user' => $user,
            'typeForm' => $typeForm->createView(),
            'vehiculeForm' => $vehiculeFormView,
            'preferencesForm' => $preferencesFormView,
            'type' => $user->getTypeUtilisateur(),
            'trajetForm' => $trajetFormView,
            'hasVehicule' => $hasVehicule,
        ]);
    }

    private function handleVehiculeForm(Request $request, EntityManagerInterface $em, User $user)
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeFormType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vehicule->setUser($user);
            $em->persist($vehicule);
            $em->flush();
            return $this->redirectToRoute('compte_utilisateur', [], Response::HTTP_SEE_OTHER);
        }

        return $form->createView();
    }

    private function handlePreferencesForm(Request $request, EntityManagerInterface $em, User $user)
    {
        $preferences = new Preferences();
        $form = $this->createForm(PreferencesFormType::class, $preferences);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $preferences->setUser($user);
            $em->persist($preferences);
            $em->flush();
            return $this->redirectToRoute('compte_utilisateur', [], Response::HTTP_SEE_OTHER);
        }

        return $form->createView();
    }

    private function handleTrajetForm(Request $request, EntityManagerInterface $em, User $user): array
    {
        $vehicule = $em->getRepository(Vehicule::class)->findOneBy(['user' => $user]);

        if (!$vehicule) {
            return [null, false];
        }

        $trajet = new Trajet();
        $trajet->setConducteur($user);
        $trajet->setVehicule($vehicule);

        $form = $this->createForm(TrajetForm::class, $trajet, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($trajet);
            $em->flush();
            return [$this->redirectToRoute('compte_utilisateur', [], Response::HTTP_SEE_OTHER), true];
        }

        return [$form->createView(), true];
    }

    #[Route('/utilisateur/type', name: 'compte_type_change', methods: ['POST'])]
     function ajaxUpdateType(Request $request, EntityManagerInterface $em): JsonResponse
    {
    $user = $this->getUser();
    if (!$user instanceof User) {
        return new JsonResponse(['error' => 'Non connecté'], 401);
    }

    $data = json_decode($request->getContent(), true);
    if (!isset($data['typeUtilisateur'])) {
        return new JsonResponse(['error' => 'Type manquant'], 400);
    }

    $type = $data['typeUtilisateur'];
    if (!in_array($type, ['conducteur', 'passager'])) {
        return new JsonResponse(['error' => 'Type invalide'], 400);
    }

    $user->setTypeUtilisateur($type);
    $em->flush();

    return new JsonResponse(['success' => true]);
  }

}
