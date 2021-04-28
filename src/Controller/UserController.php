<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\EditProfileType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
     /**
     * @Route("/profil", name="user-profile")
     */
    public function user()
    {
            return $this->render('user/informations.html.twig');
    }

    /**
     * @Route("/profil/modifier", name="edit-profile")
     */
    public function editProfile(Request $request)
    {
        $profileUser = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $profileUser);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($profileUser);
            $manager->flush();

            return $this->redirectToRoute('user');
        }

        return $this->render('user/editprofile.html.twig', [
            'editProfileForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function usersAdmin(PaginatorInterface $paginator, UserRepository $userRepository, Request $request): Response
    {
        $allUsers = $paginator->paginate(
            $user = $userRepository->findAll(),
            $request->query->getInt('page', 1),
            20,
        );

        return $this->render('admin/users.html.twig', [
            'allUsers' => $allUsers,
        ]);
    }

     /**
     * @Route("/admin/users/update-{id}", name="user_update")
     */
    public function updateUser(UserRepository $userRepository, $id, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $userRepository->find($id);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('admin_users');
        }
        return $this->render('admin/userForm.html.twig', [
            'userForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/users/delete-{id}", name="user_delete")
     */
    public function deleteUser(UserRepository $userRepository, $id)
    {
        $user = $userRepository->find($id);
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($user);
        $manager->flush();
        return $this->redirectToRoute('admin_users');
    }
}
