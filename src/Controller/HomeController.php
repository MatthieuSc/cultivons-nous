<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function contact(Request $request, \Swift_Mailer $mailer): Response
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

            return $this->redirectToRoute('home');

        }
    
        return $this->render('home/index.html.twig', [
            'contactForm' => $contactForm->createView(),
        ]);
    }
}
