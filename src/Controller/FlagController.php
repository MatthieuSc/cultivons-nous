<?php

namespace App\Controller;

use App\Entity\Flag;
use App\Form\FlagType;
use App\Form\ContactType;
use App\Repository\FlagRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FlagController extends AbstractController
{

    /**
     * @Route("/capitale-au-hasard", name="capital_random")
     */
    public function findRandomFlag(FlagRepository $flagRepository, Request $request, \Swift_Mailer $mailer): Response
    {
        $contactForm = $this->createForm(ContactType::class);
        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $contact = $contactForm->getData();

            $message = (new \Swift_Message('Nouveau contact'))
                ->setFrom($contact['email'])
                ->setTo('contact.matthieu.scherer@gmail.com')
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig',
                        compact('contact')
                    ),
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash('message', "le message à bien été envoyé.");
        }

        $cap = $flagRepository->findOneRandomFlag();

        return $this->render('flag/index.html.twig', [
            'cap' => $cap,
            'contactForm' => $contactForm->createView()
        ]);
    }

    /**
     * @Route("/capitale", name="capitals_list")
     */
    public function findFlag(FlagRepository $flagRepository, Request $request, \Swift_Mailer $mailer, PaginatorInterface $paginator): Response
    {
        $contactForm = $this->createForm(ContactType::class);
        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $contact = $contactForm->getData();

            $message = (new \Swift_Message('Nouveau contact'))
                ->setFrom($contact['email'])
                ->setTo('contact.matthieu.scherer@gmail.com')
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig',
                        compact('contact')
                    ),
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash('message', "le message à bien été envoyé.");
        }

        $allCaps = $paginator->paginate(
            $flagRepository->findAll(),
            $request->query->getInt("page", 1),
            20,
        );

        return $this->render('flag/list.html.twig', [
            'allCaps' => $allCaps,
            'contactForm' => $contactForm->createView()
        ]);
    }

    /**
     * @Route("/admin/capitale", name="admin_capitals_list")
     */
    public function adminFlag(PaginatorInterface $paginator, FlagRepository $flagRepository, Request $request): Response
    {

        $allCaps = $paginator->paginate(
            $flagRepository->findAll(),
            $request->query->getInt("page", 1),
            20,
        );

        return $this->render('admin/flag.html.twig', [
            'allCaps' => $allCaps,
        ]);
    }

    /**
     * @Route("/admin/capitale/create", name="capital_create")
     */
    public function createFlag(Request $request)
    {
        $flag = new Flag();
        $form = $this->createForm(FlagType::class, $flag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $infoImgFlag = $form['flag']->getData(); // récupère les infos de l'image 1
            $extensionImgFlag = $infoImgFlag->guessExtension(); // récupère le format de l'image 1
            $nomImgFlag = '1-' . time() . '.' . $extensionImgFlag; // compose un nom d'image unique
            $infoImgFlag->move($this->getParameter('dossier_images_drapeaux'), $nomImgFlag); // déplace l'image
            $flag->setFlag($nomImgFlag);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($flag);
            $manager->flush();
            return $this->redirectToRoute('admin_capitals_list');
        }
        return $this->render('admin/flagForm.html.twig', [
            'flagForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/capitale/update-{id}", name="capital_update")
     */
    public function updateFlag(FlagRepository $flagRepository, $id, Request $request)
    {
        $flag = $flagRepository->find($id);
        $form = $this->createForm(FlagType::class, $flag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $oldNomFlag = $flag->getFlag();
            $oldCheminFlag = $this->getParameter('dossier_images_drapeaux') . '/' . $oldNomFlag;
            if (file_exists($oldCheminFlag)) {
                unlink($oldCheminFlag);
            }
            $infoImgFlag = $form['flag']->getData(); // récupère les infos de l'image 1
            $extensionImgFlag = $infoImgFlag->guessExtension(); // récupère le format de l'image 1
            $nomImgFlag = '1-' . time() . '.' . $extensionImgFlag; // compose un nom d'image unique
            $infoImgFlag->move($this->getParameter('dossier_images_drapeaux'), $nomImgFlag); // déplace l'image
            $flag->setFlag($nomImgFlag);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($flag);
            $manager->flush();
            return $this->redirectToRoute('admin_capitals_list');
        }
        return $this->render('admin/flagForm.html.twig', [
            'flagForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/capitale/delete-{id}", name="capital_delete")
     */
    public function deleteFlag(FlagRepository $flagRepository, $id)
    {
        $flag = $flagRepository->find($id);
        $oldNomFlag = $flag->getFlag();
        $oldCheminFlag = $this->getParameter('dossier_images_drapeaux') . '/' . $oldNomFlag;
        if (file_exists($oldCheminFlag)) {
            unlink($oldCheminFlag);
        }
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($flag);
        $manager->flush();
        return $this->redirectToRoute('admin_capitals_list');
    }
}
