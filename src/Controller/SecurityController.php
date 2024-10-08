<?php
/**
 * Security controller.
 */

namespace App\Controller;

use App\Form\Type\UserPasswordChangeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\UserServiceInterface;

/**
 * Class SecurityController.
 */
class SecurityController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface $userService User service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Login action.
     *
     * @param AuthenticationUtils $authenticationUtils Authenticator
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Logout action.
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
    }

    /**
     * @param Request                     $request        Request
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     *
     * @return Response response
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/change-password', name: 'app_change_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        if (!$user instanceof PasswordAuthenticatedUserInterface) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(UserPasswordChangeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            if ($passwordHasher->isPasswordValid($user, $currentPassword)) {
                $encodedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($encodedPassword);

                $this->userService->save($user);

                $this->addFlash('success', $this->translator->trans('Successfully edited'));

                return $this->redirectToRoute('news_index');
            }

            $form->get('currentPassword')->addError(new FormError($this->translator->trans('Incorrect password')));
        }

        return $this->render('security/change_password.html.twig', ['changePasswordForm' => $form->createView()]);
    }
}
