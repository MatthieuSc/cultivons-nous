<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Entity\ImprobableInformation;
use App\Form\ImprobableInformationType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ImprobableInformationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImprobableInformationController extends AbstractController
{
    /**
     * @Route("/information", name="random_improbable_information")
     */
    public function randomInformation(Request $request, \Swift_Mailer $mailer, ImprobableInformationRepository $improbableInformationRepository): Response
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

        $information = $improbableInformationRepository->findOneRandomInfo();
        return $this->render('improbable_information/index.html.twig', [
            'information' => $information,
            'contactForm' => $contactForm->createView()
        ]);
    }

     /**
     * @Route("/listes-infos", name="improbable_information_list")
     */
    public function informationsList(\Swift_Mailer $mailer, PaginatorInterface $paginator, ImprobableInformationRepository $improbableInformationRepository, Request $request): Response
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
        
        $informations = $paginator->paginate(
            $improbableInformationRepository->findAll(),
            $request->query->getInt('page', 1),
            5,
        );
        return $this->render('improbable_information/list.html.twig', [
            'informations' => $informations,
            'contactForm' => $contactForm->createView()
        ]);
    }

    /**
     * @Route("/admin/infos-impro-list", name="admin_impro_list")
     */
    public function adminInfosList(PaginatorInterface $paginator, ImprobableInformationRepository $ImprobableInformationRepository, Request $request): Response
    {

        $allInfos = $paginator->paginate(
            $ImprobableInformationRepository->findAll(),
            $request->query->getInt("page", 1),
            20,
    );
    
        return $this->render('admin/improinfos.html.twig', [
            'allInfos' => $allInfos,
        ]);
    }

    /**
     * @Route("/admin/infosimpro/create", name="improbable_informations_create")
     */
    public function createInfosImpro(Request $request)
    {
        $improInfo = new ImprobableInformation();
        $form = $this->createForm(ImprobableInformationType::class, $improInfo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($improInfo);
            $manager->flush();
            return $this->redirectToRoute('admin_impro_list');
       }
        return $this->render('admin/improinfosForm.html.twig', [
            'improInfoForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/users/update-{id}", name="impro_info_update")
     */
    public function updateImproInfo(ImprobableInformationRepository $improInfoRepository, $id, Request $request)
    {
        $improInfo = $improInfoRepository->find($id);
        $form = $this->createForm(ImprobableInformationType::class, $improInfo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($improInfo);
            $manager->flush();
            return $this->redirectToRoute('admin_impro_list');
        }
        return $this->render('admin/improinfosForm.html.twig', [
            'improInfoForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/info-impro/delete-{id}", name="impro_info_delete")
     */
    public function deleteImproInfo(ImprobableInformationRepository $improInfoRepository, $id)
    {
        $improInfo = $improInfoRepository->find($id);
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($improInfo);
        $manager->flush();
        return $this->redirectToRoute('admin_impro_list');
    }
}
