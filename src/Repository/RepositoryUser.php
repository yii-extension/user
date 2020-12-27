<?php

declare(strict_types=1);

namespace Yii\Extension\User\Repository;

use RuntimeException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;
use Yii\Extension\Service\MailerService;
use Yii\Extension\User\ActiveRecord\Profile;
use Yii\Extension\User\ActiveRecord\User;
use Yii\Extension\User\ActiveRecord\Token;
use Yii\Extension\User\Form\FormRegister;
use Yiisoft\Aliases\Aliases;
use Yiisoft\ActiveRecord\ActiveRecordInterface;
use Yiisoft\ActiveRecord\ActiveQuery;
use Yiisoft\ActiveRecord\ActiveQueryInterface;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Exception\Exception;
use Yiisoft\Files\FileHelper;
use Yiisoft\Form\FormModelInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Security\PasswordHasher;
use Yiisoft\Security\Random;

use function array_rand;
use function count;
use function filter_var;
use function str_shuffle;
use function str_split;

final class RepositoryUser implements IdentityRepositoryInterface
{
    private Aliases $aliases;
    private InitialAvatar $avatar;
    private ConnectionInterface $db;
    private LoggerInterface $logger;
    private Profile $profile;
    private Token $token;
    private User $user;
    private ?ActiveQuery $userQuery = null;

    public function __construct(
        Aliases $aliases,
        InitialAvatar $avatar,
        ConnectionInterface $db,
        LoggerInterface $logger,
        Profile $profile,
        Token $token,
        User $user
    ) {
        $this->aliases = $aliases;
        $this->avatar = $avatar;
        $this->db = $db;
        $this->logger = $logger;
        $this->token = $token;
        $this->profile = $profile;
        $this->user = $user;
        $this->userQuery();
    }

    public function block(User $user): bool
    {
        return (bool) $user->updateAttributes([
            'blocked_at' => time(),
            'auth_key' => Random::string(),
        ]);
    }

    public function confirm(User $user): bool
    {
        return (bool) $user->updateAttributes(['confirmed_at' => time()]);
    }

    public function create(FormRegister $formRegister): bool
    {
        if ($this->user->getIsNewRecord() === false) {
            throw new RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        if ($this->findUserByUsernameOrEmail($formRegister->getEmail())) {
            $formRegister->addError('email', 'Email already registered.');
            return false;
        }

        if ($this->findUserByUsernameOrEmail($formRegister->getUsername())) {
            $formRegister->addError('username', 'Username already registered.');
            return false;
        }

        /** @psalm-suppress UndefinedInterfaceMethod */
        $transaction = $this->db->beginTransaction();

        try {
            $password = empty($formRegister->getAttributeValue('password'))
                ? $this->generate(8)
                : $formRegister->getAttributeValue('password');

            $this->user->username($formRegister->getUsername());
            $this->user->email($formRegister->getEmail());
            $this->user->unconfirmedEmail(null);
            $this->user->password($password);
            $this->user->passwordHash($password);
            $this->user->authKey();
            $this->user->registrationIp($formRegister->getAttributeValue('ip'));
            $this->user->confirmedAt();
            $this->user->createdAt();
            $this->user->updatedAt();
            $this->user->flags(0);

            if (!$this->user->save()) {
                $transaction->rollBack();
                return false;
            }

            $this->generateAvatar();

            $this->profile->link('user', $this->user);

            $transaction->commit();

            $result = true;
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->logger->log(LogLevel::WARNING, $e->getMessage());
            throw $e;
        }

        return $result;
    }

    /**
     * @param string $id
     *
     * @return IdentityInterface|null
     *
     * @psalm-suppress InvalidReturnType, InvalidReturnStatement
     */
    public function findIdentity(string $id): ?IdentityInterface
    {
        return $this->findUserByCondition(['id' => $id]);
    }

    /**
     * @param string $token
     * @param string|null $type
     *
     * @return IdentityInterface|null
     *
     * @psalm-suppress InvalidReturnType, InvalidReturnStatement
     */
    public function findIdentityByToken(string $token, ?string $type = null): ?IdentityInterface
    {
        return $this->findUserByCondition(['auth_key' => $token]);
    }

    public function findUserAll(): array
    {
        return $this->userQuery->all();
    }

    public function findUserAllAsArray(): array
    {
        return $this->userQuery->asArray()->all();
    }

    public function findUserByCondition(array $condition): ?ActiveRecordInterface
    {
        return $this->userQuery->findOne($condition);
    }

    public function findUserById(string $id): ?ActiveRecordInterface
    {
        return $this->findUserByCondition(['id' => $id]);
    }

    public function findUserByEmail(string $email): ?ActiveRecordInterface
    {
        return $this->findUserByCondition(['email' => $email]);
    }

    public function findUserByUsername(string $username): ?ActiveRecordInterface
    {
        return $this->findUserByCondition(['username' => $username]);
    }

    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?ActiveRecordInterface
    {
        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->findUserByEmail($usernameOrEmail);
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    public function generateUrlToken(UrlGeneratorInterface $url, bool $isConfirmation): ?string
    {
        $urlToken = null;

        if ($isConfirmation === true) {
            $urlToken = $url->generateAbsolute(
                $this->token->toUrl(),
                [
                    'id' => $this->token->getAttribute('user_id'),
                    'code' => $this->token->getAttribute('code')
                ]
            );
        }

        return $urlToken;
    }

    public function loadData(User $user, FormRegister $formRegister): void
    {
        $formRegister->setAttribute('email', $user->getEmail());
        $formRegister->setAttribute('username', $user->getUsername());
    }

    public function register(FormRegister $formRegister, bool $isConfirmation, bool $isGeneratingPassword): bool
    {
        if ($this->user->getIsNewRecord() === false) {
            throw new RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        if ($this->findUserByUsernameOrEmail($formRegister->getEmail())) {
            $formRegister->addError('email', 'Email already registered.');
            return false;
        }

        if ($this->findUserByUsernameOrEmail($formRegister->getUsername())) {
            $formRegister->addError('username', 'Username already registered.');
            return false;
        }

        /** @psalm-suppress UndefinedInterfaceMethod */
        $transaction = $this->db->beginTransaction();

        try {
            $password = $isGeneratingPassword
                ? $this->generate(8)
                : $formRegister->getPassword();

            $this->user->username($formRegister->getUsername());
            $this->user->email($formRegister->getEmail());
            $this->user->unconfirmedEmail(null);
            $this->user->password($password);
            $this->user->passwordHash($password);
            $this->user->authKey();
            $this->user->registrationIp($formRegister->getAttributeValue('ip'));

            if ($isConfirmation === false) {
                $this->user->confirmedAt();
            }

            $this->user->createdAt();
            $this->user->updatedAt();
            $this->user->flags(0);

            if (!$this->user->save()) {
                $transaction->rollBack();
                return false;
            }

            if ($isConfirmation === true) {
                $this->insertToken();
            }

            $this->generateAvatar();

            $this->profile->link('user', $this->user);

            $transaction->commit();

            $result = true;
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->logger->log(LogLevel::WARNING, $e->getMessage());
            throw $e;
        }

        return $result;
    }

    public function resetPassword(User $user): bool
    {
        $user->password($this->generate(8));

        return $user->save();
    }

    public function update(User $user, FormRegister $formRegister): bool
    {
        $password = empty($formRegister->getAttributeValue('password'))
            ? $this->generate(8)
            : $formRegister->getAttributeValue('password');

        $user->username($formRegister->getUsername());
        $user->email($formRegister->getEmail());
        $user->unconfirmedEmail(null);
        $user->password($password);
        $user->passwordHash($password);
        $user->authKey();
        $user->registrationIp($formRegister->getAttributeValue('ip'));
        $user->confirmedAt();
        $user->createdAt();
        $user->updatedAt();
        $user->flags(0);

        return (bool) $user->update();
    }

    public function unblock(User $user): bool
    {
        return (bool) $user->updateAttributes(['blocked_at' => null]);
    }

    public function validatePassword(FormModelInterface $form, string $password, string $password_hash): bool
    {
        $result = true;

        if (!(new PasswordHasher())->validate($password, $password_hash)) {
            $form->addError('currentPassword', 'Invalid password.');
            $result = false;
        }

        return $result;
    }

    /**
     * Generate password.
     *
     * generates user-friendly random password containing at least one lower case letter, one uppercase letter and one
     * digit. The remaining characters in the password are chosen at random from those three sets
     *
     * @param int $length
     * @return string
     *
     * {@see https://gist.github.com/tylerhall/521810}
     */
    private function generate(int $length): string
    {
        $sets = [
            'abcdefghjkmnpqrstuvwxyz',
            'ABCDEFGHJKMNPQRSTUVWXYZ',
            '23456789',
        ];
        $all = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[array_rand($all)];
        }

        $password = str_shuffle($password);
        return $password;
    }

    private function generateAvatar(): void
    {
        $avatar = $this->avatar->name($this->user->getAttribute('username'))
            ->length(2)
            ->fontSize(0.5)
            ->size(28)
            ->background('#1e6887')
            ->color('#fff')
            ->rounded()
            ->generateSvg()
            ->toXMLString();

        FileHelper::createDirectory($this->aliases->get('@avatars'));

        file_put_contents($this->aliases->get('@avatars') . '/' . $this->user->getAttribute('id') . '.svg', $avatar);
    }

    private function insertToken(): void
    {
        $this->token->setAttribute('type', Token::TYPE_CONFIRMATION);

        $this->token->deleteAll(
            [
                'user_id' => $this->token->getAttribute('user_id'),
                'type' => $this->token->getAttribute('type')
            ]
        );

        $this->token->setAttribute('created_at', time());
        $this->token->setAttribute('code', Random::string());

        $this->token->link('user', $this->user);
    }

    private function userQuery(): ActiveQueryInterface
    {
        return $this->userQuery = new ActiveQuery(User::class, $this->db);
    }
}
