<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ContactType;
use App\Security\Authenticator;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/inscription", name="app_register")
     */
    public function register(Request $request, \Swift_Mailer $mailer, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, Authenticator $authenticator, UserRepository $userRepository): Response
    {
        $contactForm = $this->createForm(ContactType::class);
        $contactForm->handleRequest($request);

        if($contactForm->isSubmitted() && $contactForm->isValid()) {
            $contact = $contactForm->getData();
            
            $message = (new \Swift_Message('Nouveau contact'))
                ->setFrom($contact['email'])
                ->setTo('contact.matthieu.scherer@gmail.com')
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig', compact('contact') 
                    ),
                    'text/html'
                )
            ;

            $mailer->send($message);

            $this->addFlash('message', "le message à bien été envoyé.");

        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            
            $user->setActivationToken(md5(uniqid()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'contactForm' => $contactForm->createView()
        ]);
    }

    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation ($token, UserRepository $userRepository, Request $request, \Swift_Mailer $mailer)
    {
        
        
        $user = $userRepository->findOneBy(['activation_token' => $token]);

        // Si on ne trouve pas d'ulisateur avec le token, alors on envoie un message d'erreur
        if(!$user) {
            throw $this->createNotFoundException("Cette utilisateur n'existe pas");
        }

        // On supprime le token lorsque l'utilisateur est trouvé
        $user->setActivationToken(null);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();

        // Génération d'un massage succès
        $this->addFlash('message', 'Le compte a bien été activé');

        return $this->redirectToRoute('home');

    

    }

}
