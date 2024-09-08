<?php
/**
 * This file is part of the "Your Project Name".
 *
 * (c) Your Name <your.email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Comment controller.
 *
 * Handles operations related to comments.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\News; // Added missing News import
use App\Form\Type\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommentController.
 */
#[\Symfony\Component\Routing\Attribute\Route('/comment')]
class CommentController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param CommentRepository $commentRepository The comment repository
     */
    public function __construct(private readonly CommentRepository $commentRepository)
    {
    }

    /**
     * Create a new comment.
     *
     * @param int     $newsId  The ID of the news entity
     * @param Request $request The request object
     *
     * @return Response The response object
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/new/{newsId}', name: 'comment_new', methods: ['GET', 'POST'])]
    public function new(int $newsId, Request $request): Response
    {
        $news = $this->getDoctrine()->getRepository(News::class)->find($newsId);

        if (!$news) {
            throw $this->createNotFoundException(sprintf('No news found for id %d', $newsId));
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setNews($news);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Comment added successfully.');

            return $this->redirectToRoute('news_show', ['id' => $newsId]);
        }

        return $this->render('comment/new.html.twig', [
            'commentForm' => $form->createView(),
        ]);
    }

    /**
     * Show a comment.
     *
     * @param Comment $comment The comment entity
     *
     * @return Response The response object
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/{id}', name: 'comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * Edit a comment.
     *
     * @param Comment $comment The comment entity
     * @param Request $request The request object
     *
     * @return Response The response object
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/{id}/edit', name: 'comment_edit', methods: ['GET', 'POST'])]
    public function edit(Comment $comment, Request $request): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Comment updated successfully.');

            return $this->redirectToRoute('news_show', ['id' => $comment->getNews()->getId()]);
        }

        return $this->render('comment/edit.html.twig', [
            'commentForm' => $form->createView(),
        ]);
    }

    /**
     * Delete a comment.
     *
     * @param Comment $comment The comment entity
     * @param Request $request The request object
     *
     * @return Response The response object
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/{id}/delete', name: 'comment_delete', methods: ['POST'])]
    public function delete(Comment $comment, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Comment deleted successfully.');
        }

        return $this->redirectToRoute('news_show', ['id' => $comment->getNews()->getId()]);
    }
}
