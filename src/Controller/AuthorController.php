<?php

namespace App\Controller;
use App\Form\AuthorType;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\SearchFormType;
use App\Form\CustomAuthorSearchType;


class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/showAuthor/{name}', name: 'app_showAuthor')]
    public function showAuthor($name)
    {
        return $this->render('author/show.html.twig', ['n' => $name]);
    }

    #[Route('/showlist', name: 'app_showlist')]
    public function list()
    {
        $authors = array(
            array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/william-shakespeare.jpg', 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200),
            array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
        );

        // Return the authors as JSON
        return $this->json(['authors' => $authors]);
    }

    #[Route('/authorDetails/{id}', name: 'app_authorDetails')]
    public function authorDetails($id): Response
    {
        // Get the author's ID from the URL parameter
        $authorId = (int) $id;

        // Call the 'list' function to get the list of authors
        $response = $this->list();

        // Check if 'authors' key exists in the response
        if (!array_key_exists('authors', json_decode($response->getContent(), true))) {
            throw $this->createNotFoundException('Authors not found.');
        }

        $authors = json_decode($response->getContent(), true)['authors'];

        // Find the author by ID in the list
        $author = null;
        foreach ($authors as $a) {
            if ($a['id'] === $authorId) {
                $author = $a;
                break;
            }
        }

        if (!$author) {
            throw $this->createNotFoundException('Author not found.');
        }

        return $this->render('author/showAuthor.html.twig', [
            'author' => $author,
        ]);
    }
    #[Route('/Affiche', name: 'app_Affiche')]

    public function Affiche (AuthorRepository $repository)
        {
            $author=$repository->findAll() ; //select *
            return $this->render('author/Affiche.html.twig',['author'=>$author]);
        }
        #[Route('/AddStatistique', name: 'app_AddStatistique')]
    public function addStatistique(EntityManagerInterface $entityManager)
    {
        // Créez une instance de l'entité Author
        $author1 = new Author();
        $author1->setUsername("test"); // Utilisez "setUsername" pour définir le nom d'utilisateur
        $author1->setEmail("test@gmail.com"); // Utilisez "setEmail" pour définir l'e-mail

        // Enregistrez l'entité dans la base de données
        $entityManager->persist($author1);
        $entityManager->flush();

        return $this->redirectToRoute('app_Affiche'); // Redirigez vers la route 'app_Affiche'
    }
    #[Route('/Add', name: 'app_Add')]
    public function Add(Request $request, EntityManagerInterface $entityManager)
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->add('Ajouter', SubmitType::class);
    
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($author);
            $entityManager->flush();
            return $this->redirectToRoute('app_Affiche');
        }
    
        return $this->render('author/Add.html.twig', ['f' => $form->createView()]);
    }
    
    #[Route('/edit/{id}', name: 'app_edit')]
    public function edit(AuthorRepository $repository, $id, Request $request, EntityManagerInterface $entityManager)
    {
        $author = $repository->find($id);
        $form = $this->createForm(AuthorType::class, $author);
        $form->add('Edit', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Utilisez le service EntityManager pour enregistrer les modifications en base de données.
            return $this->redirectToRoute("app_Affiche");
        }

        return $this->render('author/edit.html.twig', [
            'f' => $form->createView(),
        ]);
    }
    
    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete($id, EntityManagerInterface $entityManager, AuthorRepository $repository)
    {
        $author = $repository->find($id);
    
        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }
    
        $entityManager->remove($author);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_Affiche');
    }
    #[Route('/authors-by-email', name: 'app_authorsByEmail')]
    public function listAuthorsByEmail(AuthorRepository $authorRepository): Response
    {
        $authors = $authorRepository->listAuthorByEmail();

        return $this->render('author/list_by_email.html.twig', [
            'authors' => $authors,
        ]);
    }
    #[Route('/searchAuthors', name: 'app_search_authors')]
    public function searchAuthors(Request $request, AuthorRepository $authorRepository)
    {
        $form = $this->createForm(CustomAuthorSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $minBooks = $form->get('minBooks')->getData();
            $maxBooks = $form->get('maxBooks')->getData();

            $authors = $authorRepository->findAuthorsByBookCountRange($minBooks, $maxBooks);

            return $this->render('author/search_results.html.twig', [
                'authors' => $authors,
            ]);
        }

        return $this->render('author/search.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }
    #[Route('/deleteAuthorsWithZeroBooks', name:'app_delete_authors_with_zero_books')]
  
   public function deleteAuthorsWithZeroBooks(AuthorRepository $authorRepository)
   {
       $authorRepository->deleteAuthorsWithZeroBooks();
   
       return $this->redirectToRoute('app_Affiche'); // Redirigez vers la page souhaitée après la suppression.
   }
}    
