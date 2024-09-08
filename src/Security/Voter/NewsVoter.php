<?php
/**
 * News voter.
 */

namespace App\Security\Voter;

use App\Entity\News;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class NewsVoter.
 */
class NewsVoter extends Voter
{
    /**
     * Edit permission.
     *
     * @const string
     */
    private const EDIT = 'EDIT';

    /**
     * View permission.
     *
     * @const string
     */
    private const VIEW = 'VIEW';

    /**
     * Delete permission.
     *
     * @const string
     */
    private const DELETE = 'DELETE';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool Result
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof News;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute Permission name
     * @param mixed          $subject   Object
     * @param TokenInterface $token     Security token
     *
     * @return bool Vote result
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$subject instanceof News) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $user instanceof UserInterface && $this->canEdit($subject, $user),
            self::VIEW => $this->canView($subject, $user),
            self::DELETE => $user instanceof UserInterface && $this->canDelete($subject, $user),
            default => false,
        };
    }

    /**
     * Checks if user can edit news.
     *
     * @param News          $news News entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canEdit(News $news, UserInterface $user): bool
    {
        return $news->getAuthor() === $user;
    }

    /**
     * Checks if user can view news.
     *
     * @param News               $news News entity
     * @param UserInterface|null $user User (can be null for unauthenticated users)
     *
     * @return bool Result
     */
    private function canView(News $news, ?UserInterface $user = null): bool
    {
        // Allow public access (all users can view the news)
        return true;
    }

    /**
     * Checks if user can delete news.
     *
     * @param News          $news News entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canDelete(News $news, UserInterface $user): bool
    {
        return $news->getAuthor() === $user;
    }
}
