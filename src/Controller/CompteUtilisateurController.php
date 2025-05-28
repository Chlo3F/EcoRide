<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Trajet;
use App\Entity\Vehicule;
use App\Entity\Preferences;
use App\Form\TrajetFormType;
use App\Form\VehiculeFormType;
use App\Form\TypeUtilisateurFormType;
use App\Form\PreferencesFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompteUtilisateurController extends AbstractController
{
    #[Route('/compte/vehicule', name: 'submit_vehicule', methods: ['POST'])]
    public function submitVehicule(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeFormType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vehicule->setUser($user);
            $em->persist($vehicule);
            $em->flush();

            return new Response('<div class="alert alert-success">Véhicule enregistré !</div>');
        }

        return $this->render('partials/_vehicule_form.html.twig', [
            'vehiculeForm' => $form->createView()
        ]);
    }

    #[Route('/compte/preferences', name: 'submit_preferences', methods: ['POST'])]
    public function submitPreferences(Request $request, EntityManagerInterface $em): Response
    {
        $loggedUser = $this->getUser();

        if (!$loggedUser instanceof User) {
            // Ici, si tu es dans un contexte API ou tu as un objet UserInterface simplifié
            // Il faut récupérer l'entité complète avec l'identifiant, par exemple email
            $userIdentifier = $loggedUser->getUserIdentifier();

            $user = $em->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);

            if (!$user) {
                throw $this->createNotFoundException('Utilisateur non trouvé.');
            }
        } else {
            $user = $loggedUser;
        }

        $preferences = $user->getPreferences()->first();

        if (!$preferences) {
            $preferences = new Preferences();
            $preferences->setUser($user);
        }

        $form = $this->createForm(PreferencesFormType::class, $preferences);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($preferences);
            $em->flush();

            return new Response('<div class="alert alert-success">Préférences enregistrées !</div>');
        }

        return $this->render('partials/_preferences_form.html.twig', [
            'preferencesForm' => $form->createView()
        ]);
    }

    #[Route('/compte/trajet', name: 'submit_trajet', methods: ['POST'])]
    public function submitTrajet(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $trajet = new Trajet();

        $form = $this->createForm(TrajetFormType::class, $trajet, [
            'user' => $user
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trajet->setConducteur($user);
            $em->persist($trajet);
            $em->flush();

            return new Response('<div class="alert alert-success">Trajet enregistré !</div>');
        }

        return $this->render('partials/_trajet_form.html.twig', [
            'trajetForm' => $form->createView()
        ]);
    }


    #[Route('/compte', name: 'compte_utilisateur', methods: ['GET'])]
    public function compte(EntityManagerInterface $em): Response
    {
        $loggedUser = $this->getUser();

        if (!$loggedUser instanceof User) {
            // récupère l'entité User complète depuis la BDD via l'id ou email
            // ici on suppose que getUserIdentifier() retourne un identifiant unique (email par ex)
            $user = $em->getRepository(User::class)->findOneBy(['email' => $loggedUser->getUserIdentifier()]);

            if (!$user) {
                throw $this->createNotFoundException('Utilisateur non trouvé.');
            }
        } else {
            $user = $loggedUser;
        }

        $typeForm = $this->createForm(TypeUtilisateurFormType::class);
        $vehiculeForm = $this->createForm(VehiculeFormType::class);

        $preferences = $user->getPreferences()->first() ?: null;
        $preferencesForm = $this->createForm(PreferencesFormType::class, $preferences);

        $trajetForm = $this->createForm(TrajetFormType::class, null, [
            'user' => $user,
        ]);

        return $this->render('compte_utilisateur.html.twig', [
            'typeForm' => $typeForm,
            'vehiculeForm' => $vehiculeForm,
            'preferencesForm' => $preferencesForm,
            'trajetForm' => $trajetForm,
        ]);
    }
}
