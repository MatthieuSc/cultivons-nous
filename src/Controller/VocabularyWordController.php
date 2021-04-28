<?php

namespace App\Controller;

use App\Entity\VocabularyWord;
use App\Form\VocabularyWordType;
use App\Form\ImprobableInformationType;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\VocabularyWordRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ImprobableInformationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VocabularyWordController extends AbstractController
{
    /**
     * @Route("/word", name="random_vocabulary_word")
     */
    public function randomWord(VocabularyWordRepository $vocabularyWordRepository): Response
    {

        $word = $vocabularyWordRepository->findOneRandomWord();
        return $this->render('vocabulary_word/index.html.twig', [
            'word' => $word,
        ]);
    }

    /**
     * @Route("/words-list", name="vocabulary_word_list")
     */
    public function wordsList(PaginatorInterface $paginator,VocabularyWordRepository $vocabularyWordRepository, Request $request): Response
    {

        $words = $paginator->paginate(
            $vocabularyWordRepository->findAllByAlphaOrder(),
            $request->query->getInt('page', 1),
            5,
        );

        return $this->render('vocabulary_word/list.html.twig', [
            'words' => $words,
        ]);
    }

     /**
     * @Route("/admin/words-list", name="admin_words_list")
     */
    public function adminWordsList(PaginatorInterface $paginator, VocabularyWordRepository $vocabularyWordRepository, Request $request): Response
    {

        $allWords = $paginator->paginate(
            $vocabularyWordRepository->findAllByAlphaOrder(),
            $request->query->getInt("page", 1),
            20,
    );
    
        return $this->render('admin/wordslist.html.twig', [
            'allWords' => $allWords,
        ]);
    }

     /**
     * @Route("/admin/vocabulary-word/create", name="vocab_word_create")
     */
    public function createVocabWord(Request $request)
    {
        $vocabWord = new VocabularyWord();
        $form = $this->createForm(VocabularyWordType::class, $vocabWord);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($vocabWord);
            $manager->flush();
            return $this->redirectToRoute('admin_word_list');
       }
        return $this->render('admin/vocabwordsForm.html.twig', [
            'vocabWordForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/vocabulaire/update-{id}", name="vocab_word_update")
     */
    public function updateImproInfo(VocabularyWordRepository $vocabWordRepository, $id, Request $request)
    {
        $vocabWord = $vocabWordRepository->find($id);
        $form = $this->createForm(VocabularyWordType::class, $vocabWord);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($vocabWord);
            $manager->flush();
            return $this->redirectToRoute('admin_words_list');
        }
        return $this->render('admin/vocabwordsForm.html.twig', [
            'vocabWordForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/vocabulaire/delete-{id}", name="vocab_word_delete")
     */
    public function deleteVocabWord(VocabularyWordRepository $vocabWordRepository, $id)
    {
        $vocabWord = $vocabWordRepository->find($id);
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($vocabWord);
        $manager->flush();
        return $this->redirectToRoute('admin_words_list');
    }
}
