<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Author;
use App\Repository\BookRepository;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraint;

class BookController extends AbstractController
{
    /**
     * @Route("/books", name="book_list")
     */
    public function index(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();
        return $this->json($books, 200);
    }

    /**
     * @Route("/book/create", name="book.create")
     */
    public function create(Request $request, BookRepository $bookRepository, AuthorRepository $authorRepository): Response
    {
        $authors = $authorRepository->findAll();
        $choices = [];
        foreach ($authors as $author) {
            $choices[$author->getFirstname()] = $author->getId();
        }
        $form = $this->createFormBuilder()
            ->add('title', TextType::class, [
                'label'    => 'Title',
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('image', FileType::class, [
                'label'       => 'Upload Image (Max Size: 2MB, Allowed Types: .png, .jpg)',
                'required'    => false,
                'constraints' => [
                    new File([
                        'maxSize'          => '2M',
                        'mimeTypes'        => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid .png or .jpg image',
                    ]),
                ],
            ])
            ->add('authors', ChoiceType::class, [
                'choices'     => $choices,
                'placeholder' => 'Select an option',
                'label'       => 'Your Select Field',
                'multiple'    => true, // Allow multiple selections
                'expanded'    => true, // Render as checkboxes (optional, for a more user-friendly UI)
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit',
            ])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $newBook = new Book($form->getData());
            $bookRepository->add($newBook, true);
        }
            return $this->render('book/create.html.twig', [
                'controller_name' => 'BookController',
                'form'            => $form->createView(),
            ]);
        }

        /**
         * @Route("/book/{id}", name="book.show")
         */
        public
        function show($id, BookRepository $bookRepository): Response
        {
            $book = $bookRepository->find($id);
            return $this->json($book, 200);
        }

        /**
         * @Route("/book/edit/{id}", name="book.edit")
         */
        public
        function edit(Request $request, $id, BookRepository $bookRepository, AuthorRepository $authorRepository): Response
        {
            $book = $bookRepository->find($id);
            $authors = $authorRepository->findAll();
            $choices = [];
            foreach ($authors as $author) {
                $choices[$author->getFirstname()] = $author->getId();
            }
            $form = $this->createFormBuilder()
                ->add('title', TextType::class, [
                    'label'    => 'Title',
                    'required' => true,
                ])
                ->add('description', TextType::class, [
                    'label' => 'Description',
                ])
                //TODO: Upload image
                ->add('image', FileType::class, [
                    'label'       => 'Upload Image (Max Size: 2MB, Allowed Types: .png, .jpg)',
                    'required'    => false,
                    'constraints' => [
                        new File([
                            'maxSize'          => '2M',
                            'mimeTypes'        => [
                                'image/jpeg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid .png or .jpg image',
                        ]),
                    ],
                ])
                ->add('authors', ChoiceType::class, [
                    'choices'     => $choices,
                    'placeholder' => 'Select an option',
                    'label'       => 'Your Select Field',
                    'multiple'    => true, // Allow multiple selections
                    'expanded'    => true, // Render as checkboxes (optional, for a more user-friendly UI)
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'Submit',
                ])
                ->getForm();
            $form->handleRequest($request);
            $newData = $form->getData();
            if($form->isSubmitted() && $form->isValid()) {
                $book->setTitle($newData['title']);
                $book->setDescription($newData['description']);
                $book->setImage($newData['image']);
//            $book->setAuthors($newData['authors']);
            }

            return $this->render('author/create.html.twig', [
                'controller_name' => 'BookController',
                'form'            => $form->createView(),
            ]);
        }


    }
