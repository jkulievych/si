<?php
/**
 * News controller.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\News;
use App\Form\Type\NewsType;
use App\Service\NewsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use App\Form\Type\RatingType;
use App\Form\Type\CommentType;
use App\Repository\NewsRepository;

/**
 * Class NewsController.
 */
#[\Symfony\Component\Routing\Attribute\Route('/news')]
class NewsController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param CategoryRepository   $categoryRepository Category repository
     * @param TagRepository        $tagRepository      Tag repository
     * @param NewsServiceInterface $newsService        News service
     * @param TranslatorInterface  $translator         Translator
     * @param NewsRepository       $newsRepository     News repository
     */
    public function __construct(private readonly CategoryRepository $categoryRepository, private readonly TagRepository $tagRepository, private readonly NewsServiceInterface $newsService, private readonly TranslatorInterface $translator, private readonly NewsRepository $newsRepository)
    {
    }

    /**
     * Index action.
     *
     * @param Request $request Request
     *
     * @return Response response
     */
    #[\Symfony\Component\Routing\Attribute\Route(name: 'news_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $user = $this->getUser();

        $categoryId = $request->query->get('categoryId');
        $tagId = $request->query->get('tagId');

        $categoryId = ctype_digit($categoryId) ? (int) $categoryId : null;
        $tagId = ctype_digit($tagId) ? (int) $tagId : null;

        $categories = $this->categoryRepository->findAll();
        $tags = $this->tagRepository->findAll();

        if ($this->isGranted('ROLE_ADMIN') || !$this->getUser() instanceof \Symfony\Component\Security\Core\User\UserInterface) {
            $pagination = $this->newsService->getAllPaginatedList($page, $categoryId, $tagId);
        } else {
            $pagination = $this->newsService->getPaginatedList($page, $user, $categoryId, $tagId);
        }

        return $this->render('news/index.html.twig', ['pagination' => $pagination, 'categories' => $categories, 'tags' => $tags]);
    }

    /**
     * Show action.
     *
     * @param News $news News entity
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}', name: 'news_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    #[IsGranted('VIEW', subject: 'news')]
    public function show(News $news): Response
    {
        return $this->render('news/show.html.twig', ['news' => $news]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/create', name: 'news_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash(
                'danger',
                $this->translator->trans('Access denied')
            );

            return $this->redirectToRoute('news_index');
        }
        $news = new News();
        $news->setAuthor($this->getUser());
        $form = $this->createForm(
            NewsType::class,
            $news,
            ['action' => $this->generateUrl('news_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->newsService->save($news);

            $this->addFlash('success', $this->translator->trans('Successfully created!'));

            return $this->redirectToRoute('news_index');
        }

        return $this->render('news/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param News    $news    News entity
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/edit', name: 'news_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('IS_AUTHENTICATED_FULLY', subject: 'news')]
    public function edit(Request $request, News $news): Response
    {
        $form = $this->createForm(NewsType::class, $news, ['method' => 'PUT', 'action' => $this->generateUrl('news_edit', ['id' => $news->getId()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->newsService->save($news);

            $this->addFlash(
                'success',
                $this->translator->trans('Successfully edited')
            );

            return $this->redirectToRoute('news_index');
        }

        return $this->render('news/edit.html.twig', ['form' => $form->createView(), 'news' => $news]);
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param News    $news    News entity
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/delete', name: 'news_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('VIEW', subject: 'news')]
    public function delete(Request $request, News $news): Response
    {
        $form = $this->createForm(FormType::class, $news, ['method' => 'DELETE', 'action' => $this->generateUrl('news_delete', ['id' => $news->getId()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->newsService->delete($news);

            $this->addFlash(
                'success',
                $this->translator->trans('Successfully deleted')
            );

            return $this->redirectToRoute('news_index');
        }

        return $this->render('news/delete.html.twig', ['form' => $form->createView(), 'news' => $news]);
    }

    /**
     * Rate action.
     *
     * @param Request        $request        Request
     * @param News           $news           News
     * @param NewsRepository $newsRepository News repository
     *
     * @return Response response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/rate', name: 'news_rate', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    public function rate(Request $request, News $news, NewsRepository $newsRepository): Response
    {
        $form = $this->createForm(RatingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ratingValue = $form->get('rating')->getData();

            $currentRating = $news->getRating() ?? 0;
            $ratingCount = $news->getRatingCount() ?? 0;

            $newRating = (($currentRating * $ratingCount) + $ratingValue) / ($ratingCount + 1);
            $news->setRating($newRating);
            $news->setRatingCount($ratingCount + 1);

            $newsRepository->add($news, true);

            return $this->redirectToRoute('news_show', ['id' => $news->getId()]);
        }

        return $this->render('news/rate.html.twig', ['news' => $news, 'form' => $form->createView()]);
    }

    /**
     * Save action for Comment.
     *
     * @param Request $request HTTP request
     * @param News    $news    News entity
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/comment', name: 'news_comment', methods: 'GET|POST')]
    public function comment(Request $request, News $news): Response
    {
        $comment = new Comment();
        $comment->setNews($news);
        $form = $this->createForm(
            CommentType::class,
            $comment,
            ['action' => $this->generateUrl('news_comment', ['id' => $news->getId()])]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->newsService->comment($comment);

            $this->addFlash('success', $this->translator->trans('Comment successfully added!'));

            return $this->redirectToRoute('news_show', ['id' => $news->getId()]);
        }

        return $this->render('news/comment.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Delete a comment.
     *
     * @param Comment $comment The comment entity to delete
     *
     * @return Response The response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/commentDelete', name: 'comment_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('VIEW', subject: 'comment')]
    public function commentDelete(Comment $comment): Response
    {
        $newsId = $comment->getNews()->getId();
        $this->newsService->deleteComment($comment);

        $this->addFlash(
            'success',
            $this->translator->trans('Comment successfully deleted')
        );

        return $this->redirectToRoute('news_show', ['id' => $newsId]);
    }
}
