<?php

namespace App\Controller;

use App\Repository\TrajetRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class TrajetResultatController extends AbstractController
{
    #[Route('/trajets/recherche', name: 'trajet_recherche')]
    public function recherche(Request $request, TrajetRepository $trajetRepository, PaginatorInterface $paginator): Response
    {
        $villeDepart = $request->query->get('villeDepart');
        $villeArrivee = $request->query->get('villeArrivee');
        $date = $request->query->get('date');
        $electriqueParam = $request->query->get('electrique');
        $dureeMax = $request->query->get('duree_max');

        $electrique = null;
        if ($electriqueParam !== null) {
        $electrique = in_array(strtolower($electriqueParam), ['1', 'true', 'yes'], true);
        }

        $queryBuilder = $trajetRepository->createQueryBuilder('t')
            ->leftJoin('t.conducteur', 'c')
            ->addSelect('c');

        if ($villeDepart && strlen(trim($villeDepart)) > 0) {
            $queryBuilder->andWhere('t.villeDepart LIKE :depart')
                ->setParameter('depart', '%' . $villeDepart . '%');
        }


        if ($villeArrivee && strlen(trim($villeArrivee)) > 0) {
            $queryBuilder->andWhere('t.villeArrivee LIKE :arrivee')
                         ->setParameter('arrivee', '%' . $villeArrivee . '%');
        }

        if ($date) {
            $dateObj = DateTime::createFromFormat('Y-m-d', $date);
            if ($dateObj) {
                $queryBuilder->andWhere('DATE(t.dateHeureDepart) = :date')
                             ->setParameter('date', $dateObj->format('Y-m-d'));
            }
        }

        if ($electrique !== null) {
            $queryBuilder->andWhere('t.energieElectrique = :elec')
                         ->setParameter('elec', $electrique);
        }

        if ($dureeMax) {
            $queryBuilder->andWhere('TIMESTAMPDIFF(MINUTE, t.dateHeureDepart, t.dateHeureArrivee) <= :dureeMax')
                         ->setParameter('dureeMax', $dureeMax);
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('trajet_resultats.html.twig', [
            'trajets' => $pagination
        ]);
    }
}
