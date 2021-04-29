<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\EditProfileType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\VocabularyWordRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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

            return $this->redirectToRoute('user-profile');
        }

        return $this->render('user/editprofile.html.twig', [
            'editProfileForm' => $form->createView()
        ]);
    }

       /**
     * @Route("/historique", name="user-historical")
     */
    public function index(SessionInterface $sessionInterface, VocabularyWordRepository $vocabularyWordRepository)
    {
        
        $historique = $sessionInterface->get('historique', []);

        $monHistorique = [];

        foreach($historique as $id => $quantite) {
            $monHistorique[] = [
                'word' => $vocabularyWordRepository->find($id),
            ];
        }

        return $this->render('user/userhistorical.html.twig', [
            'mesMots' => $monHistorique,
        ]);
    }

    /**
     * @Route("/historique/ajout/{id}", name="word-add")
     */
    public function ajoutProduit($id, SessionInterface $sessionInterface) {

        $monHistorique = $sessionInterface->get('historique', []);

        if(!empty($monHistorique[$id])) {
            $monHistorique[$id]++;
        } else {
            $monHistorique[$id] = 1;
        }

        $sessionInterface->set('historique', $monHistorique);
        
        return $this->redirectToRoute("user-historical");
    }

    // /**
    //  * @Route("/panier/suppression/{id}", name="panier_supression")
    //  */
    // public function supressionProduit($id, SessionInterface $sessionInterface) {

    //     $monPanier = $sessionInterface->get('panier', []);

    //     if(!empty($monPanier[$id])) {
    //         unset($monPanier[$id]);
    //     }

    //     $sessionInterface->set('panier', $monPanier);
        
    //     return $this->redirectToRoute("panier");
    //  }


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
