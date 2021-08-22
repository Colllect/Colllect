<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="colllect_user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email", message="already_used")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @SWG\Property(type="integer", readOnly=true)
     */
    private ?int $id;

    /**
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     *
     * @Assert\Email(message="not_a_valid_email")
     * @Assert\NotBlank(message="cannot_be_blank")
     * @Assert\Length(max=255, maxMessage="too_long")
     *
     * @SWG\Property(type="string", format="email")
     */
    private ?string $email;

    /**
     * @ORM\Column(name="nickname", type="string", length=255)
     *
     * @Assert\NotBlank(message="cannot_be_blank")
     * @Assert\Length(max=255, maxMessage="too_long")
     *
     * @SWG\Property(type="string")
     */
    private ?string $nickname;

    /**
     * @var array<string>
     * @ORM\Column(name="roles", type="json")
     *
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(type="string")
     * )
     */
    private array $roles = [];

    /**
     * @ORM\Column(name="password", type="string", length=255)
     */
    private ?string $password;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(message="cannot_be_blank")
     * @Assert\Length(min="8", minMessage="too_short")
     */
    private ?string $plainPassword;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @SWG\Property(type="string", format="date-time")
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserFilesystemCredentials", mappedBy="user", cascade={"persist", "remove"})
     */
    private ?UserFilesystemCredentials $filesystemCredentials;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * @Serializer\Groups({"public"})
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @Serializer\Groups({"current"})
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Username used to authenticate the user.
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @Serializer\Groups({"current"})
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        $this->password = null; // change entity to 'dirty' for Doctrine

        return $this;
    }

    /**
     * @Serializer\Groups({"public"})
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null; // Not needed since we are using Argon2id algorithm
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * @Serializer\Groups({"public"})
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFilesystemCredentials(): ?UserFilesystemCredentials
    {
        return $this->filesystemCredentials;
    }

    public function setFilesystemCredentials(UserFilesystemCredentials $filesystemCredentials): self
    {
        $this->filesystemCredentials = $filesystemCredentials;

        // set the owning side of the relation if necessary
        if ($filesystemCredentials->getUser() !== $this) {
            $filesystemCredentials->setUser($this);
        }

        return $this;
    }
}
