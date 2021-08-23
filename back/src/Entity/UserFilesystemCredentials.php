<?php

declare(strict_types=1);

namespace App\Entity;

use App\Service\UserFilesystemCredentialsService;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * @ORM\Table(name="colllect_user_filesystem_credentials")
 * @ORM\Entity(repositoryClass="App\Repository\UserFilesystemCredentialsRepository")
 */
class UserFilesystemCredentials
{
    /**
     * @ORM\Id()
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="filesystemCredentials", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @ORM\Column(name="filesystem_provider_name", type="string", length=20)
     */
    private string $filesystemProviderName;

    /**
     * Contains credentials depending of the filesystem provider:
     *   - Dropbox: access_token.
     *
     * @ORM\Column(name="credentials", type="text")
     */
    private string $credentials;

    public function __construct(User $user, string $filesystemProviderName)
    {
        $this->user = $user;
        $this->setFilesystemProviderName($filesystemProviderName);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFilesystemProviderName(): string
    {
        return $this->filesystemProviderName;
    }

    public function setFilesystemProviderName(string $filesystemProviderName): self
    {
        $supportedFilesystemProviderNames = UserFilesystemCredentialsService::getSupportedUserFilesystemProviderNames();
        if (!\in_array($filesystemProviderName, $supportedFilesystemProviderNames, true)) {
            throw new InvalidArgumentException();
        }

        $this->filesystemProviderName = $filesystemProviderName;

        return $this;
    }

    public function getCredentials(): string
    {
        return $this->credentials;
    }

    public function setCredentials(string $credentials): self
    {
        $this->credentials = $credentials;

        return $this;
    }
}
