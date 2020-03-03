<?php

namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository; // donne le chemin pour injection de dependance
use App\Entity\Comment;
use App\Form\CommentType;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo) //dans balise index on donne le chemin (injecte la dependance)
    {   
       // $repo = $this->getDoctrine()->getRepository(Article::class); // ce qui nous permet de supprimer cette ligne

        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
            ]);
        }

    // 3 piliers pour une page : Une function/Une Route/Une reponse (affichage/redirection)
        
        /**
        * @Route("/blog/new", name="blog_create")
        * @Route("/blog/{id}/edit", name="blog_edit")
        */
                
    public function form(Article $article = null, Request $request) 
        {
            if (!$article) {
                $article = new Article();
            } 

            // $form = $this->createFormBuilder($article) // on appelle function creation formulaire
            //              ->add('title') // on lui donne les champs que l'on veut
            //              ->add('content')
            //              ->add('image')
            //              ->getForm(); // on lui demande d afficher la form

            
            // with the below method we use ArticleType form start
            $form = $this->createForm(ArticleType::class, $article);
            // with the above method we use ArticleType form end

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if (!$article->getId()) {
                   $article->setCreatedAt(new \DateTime());
                }
                
                $entityManager =$this->getDoctrine()->getManager();
                $entityManager->persist($article);
                $entityManager->flush();
            
                 return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
            }

            return $this->render('blog/create.html.twig', [
                'formArticle' => $form->createView(),
                'editMode' => $article->getId() !== null
            ]);
        
        }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */

   // public function show(ArticleRepository $repo, $id) //dans balise index on donne le chemin (injecte la dependance)

   // on peut aller plus loin et faire ça ce qui permettra de virer la ligne 38 qui cree l'article
    public function show(Article $article, Request $request)
    {   $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);

            $entityManager =$this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
        
            return $this->redirectToRoute()
        }
      //  $repo = $this->getDoctrine()->getRepository(Article::class); // ce qui nous permet de supprimer cette ligne

     //   $article = $repo->find($id);

        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }
}
