<?php

declare(strict_types=1);

namespace App\Models;

class User
{
    /*
    |--------------------------------------------------------------------------
    | PROPERTIES
    |--------------------------------------------------------------------------
    */

    private ?int $user_id = null;

    private string $username;

    private string $email;

    private string $password;

    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct(
        string $username,
        string $email
    ) {

        $this->username = trim($username);

        $this->email = trim($email);
    }

    /*
    |--------------------------------------------------------------------------
    | GETTERS
    |--------------------------------------------------------------------------
    */

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /*
    |--------------------------------------------------------------------------
    | INTERNAL ID ASSIGNMENT
    |--------------------------------------------------------------------------
    */

    public function assignId(
        int $id
    ): void {

        if ($this->user_id !== null) {

            throw new \LogicException(
                'User ID already assigned.'
            );
        }

        $this->user_id = $id;
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS METHODS
    |--------------------------------------------------------------------------
    */

    public function changeUsername(
        string $username
    ): void {

        $this->username = trim($username);
    }

    public function changeEmail(
        string $email
    ): void {

        $this->email = trim($email);
    }

    public function changePassword(
        string $password
    ): void {

        $this->password = password_hash(
            $password,
            PASSWORD_DEFAULT
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DATABASE PASSWORD HYDRATION
    |--------------------------------------------------------------------------
    */

    public function setHashedPassword(
        string $hashedPassword
    ): void {

        $this->password = $hashedPassword;
    }

    /*
    |--------------------------------------------------------------------------
    | PASSWORD VERIFICATION
    |--------------------------------------------------------------------------
    */

    // public function verifyPassword(
    //     string $password
    // ): bool {

    //     return password_verify(
    //         $password,
    //         $this->password
    //     );
    // }

    /*
    |--------------------------------------------------------------------------
    | SAFE ARRAY OUTPUT
    |--------------------------------------------------------------------------
    */

    public function toArray(): array
    {
        return [
            'user_id' => $this->getUserId(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail()
        ];
    }
}
