<?php
/**
 * Tag controller.
 */

namespace App\Controller;

use App\Entity\Tag;
use App\Form\Type\TagType;
use App\Service\TagServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TagController.
 */
#[\Symfony\Component\Routing\Attribute\Route('/tag')]
class TagController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TagServiceInterface $tagService Tag service
     * @param TranslatorInterface $translator Translator
     */
    public function __construct(private readonly TagServiceInterface $tagService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route(name: 'tag_index', methods: 'GET')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Access denied');

            return $this->redirectToRoute('news_index');
        }

        $pagination = $this->tagService->getPaginatedList($page);

        return $this->render('tag/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Tag $tag Tag entity
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}', name: 'tag_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    #[IsGranted('VIEW', subject: 'news')]
    public function show(Tag $tag): Response
    {
        return $this->render('tag/show.html.twig', ['tag' => $tag]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/create', name: 'tag_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Access denied');

            return $this->redirectToRoute('news_index');
        }
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag, ['action' => $this->generateUrl('tag_create')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->save($tag);

            $this->addFlash('success', $this->translator->trans('Successfully created!'));

            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Tag     $tag     Tag entity
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/edit', name: 'tag_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('EDIT', subject: 'news')]
    public function edit(Request $request, Tag $tag): Response
    {
        $form = $this->createForm(TagType::class, $tag, ['method' => 'PUT', 'action' => $this->generateUrl('tag_edit', ['id' => $tag->getId()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->save($tag);

            $this->addFlash('success', $this->translator->trans('Successfully created!'));

            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/edit.html.twig', ['form' => $form->createView(), 'tag' => $tag]);
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Tag     $tag     Tag entity
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/delete', name: 'tag_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('DELETE', subject: 'tag', message: 'Denied')]
    public function delete(Request $request, Tag $tag): Response
    {
        $form = $this->createForm(FormType::class, $tag, ['method' => 'DELETE', 'action' => $this->generateUrl('tag_delete', ['id' => $tag->getId()])]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->delete($tag);

            $this->addFlash('success', $this->translator->trans('Successfully deleted'));

            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/delete.html.twig', ['form' => $form->createView(), 'tag' => $tag]);
    }
}
