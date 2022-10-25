<?php

declare(strict_types=1);

namespace JDecool\Clockify\Api\User;

use JDecool\Clockify\{
    Client,
    Model\CurrentUserDto,
    Model\UserDto
};

class User
{
    private $http;

    public function __construct(Client $http)
    {
        $this->http = $http;
    }

    public function current(): CurrentUserDto
    {
        $data = $this->http->get("/user");

        return CurrentUserDto::fromArray($data);
    }

    /**
     * @return UserDto[]
     */
    public function workspaceUsers(string $workspaceId): array
    {
        $data = $this->http->get("/workspaces/$workspaceId/users");

        return array_map(
            static function(array $user): UserDto {
                return UserDto::fromArray($user);
            },
            $data
        );
    }
}
