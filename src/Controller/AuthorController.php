<?php

namespace App\Controller;

use App\Entity\Author;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\AuthorRepository;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    /**
     * @Route("/authors", name="authors_list")
     */
    public function index(AuthorRepository $authorRepository): Response
    {
        $authors = $authorRepository->findAll();
        return $this->json($authors, 200);
    }

    /**
     * @Route("/author/create", name="author.create")
     */
    public function create(Request $request, AuthorRepository $authorRepository): Response
    {
        $form = $this->createFormBuilder()
            ->add('firstname', TextType::class, [
                'label' => 'Firstname',
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'The name should consist of at least 3 letters',
                    ]),
                    ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Lastname',
                'required' => true
            ])
            ->add('fathername', TextType::class, [
                'label' => 'Fathername',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newAuthor = new Author($form->getData());
            $authorRepository->add($newAuthor, true);
        }

        return $this->render('author/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
