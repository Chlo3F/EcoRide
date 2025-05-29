<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vehicule;
use App\Entity\Preferences;
use App\Entity\Trajet;
use App\Form\VehiculeFormType;
use App\Form\PreferencesFormType;
use App\Form\TrajetFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompteUtilisateurController extends AbstractController
{
    #[Route('/compte', name: 'compte_utilisateur', methods: ['GET'])]
    public function compte(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            $user = $em->getRepository(User::class)->findOneBy([
                'email' => $user->getUserIdentifier()
            ]);

            if (!$user) {
                throw $this->createNotFoundException('Utilisateur non trouvé.');
            }
        }

        $vehiculeForm = $this->createForm(VehiculeFormType::class);
        $preferences = $user->getPreferences()->first() ?: null;
        $preferencesForm = $this->createForm(PreferencesFormType::class, $preferences);
        $trajetForm = $this->createForm(TrajetFormType::class, null, ['user' => $user]);

        return $this->render('compte_utilisateur.html.twig', [
            'vehiculeForm' => $vehiculeForm,
            'preferencesForm' => $preferencesForm,
            'trajetForm' => $trajetForm,
        ]);
    }

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

            $newForm = $this->createForm(VehiculeFormType::class, new Vehicule());

            return $this->render('partials/_vehicule_form.html.twig', [
                'vehiculeForm' => $newForm->createView(),
                'message' => 'Véhicule enregistré !'
            ]);
        }

        return $this->render('partials/_vehicule_form.html.twig', [
            'vehiculeForm' => $form->createView()
        ]);
    }

    #[Route('/compte/preferences', name: 'submit_preferences', methods: ['POST'])]
    public function submitPreferences(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            $user = $em->getRepository(User::class)->findOneBy([
                'email' => $user->getUserIdentifier()
            ]);

            if (!$user) {
                throw $this->createNotFoundException('Utilisateur non trouvé.');
            }
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

            $newForm = $this->createForm(PreferencesFormType::class, new Preferences());

            return $this->render('partials/_preferences_form.html.twig', [
                'preferencesForm' => $newForm->createView(),
                'message' => 'Préférences enregistrées !'
            ]);
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

        $form = $this->createForm(TrajetFormType::class, $trajet, ['user' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trajet->setConducteur($user);
            $em->persist($trajet);
            $em->flush();

            $newForm = $this->createForm(TrajetFormType::class, new Trajet(), ['user' => $user]);

            return $this->render('partials/_trajet_form.html.twig', [
                'trajetForm' => $newForm->createView(),
                'message' => 'Trajet enregistré !'
            ]);
        }

        return $this->render('partials/_trajet_form.html.twig', [
            'trajetForm' => $form->createView()
        ]);
    }
}
