<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EmployeeType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        PaginatorInterface $paginator
    ): Response {
        $user = new User();
        $form = $this->createForm(EmployeeType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_EMPLOYE']);
            $hashedPassword = $hasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($hashedPassword);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

        $search = $request->query->get('search');

        $queryBuilder = $userRepository->createQueryBuilder('u');

        if ($search) {
            $queryBuilder
                ->where('u.email LIKE :search OR u.roles LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('admin/dashboard.html.twig', [
            'form' => $form->createView(),
            'users' => $pagination,
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_delete_user')]
    public function delete(User $user, EntityManagerInterface $em): Response
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('error', 'Impossible de supprimer un admin.');
        } else {
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('admin_dashboard');
    }
}
