<?php

namespace App\Contracts;

interface RequestServiceInterface
{
    public function createRequest(array $data): array;
    public function processRequest(string $requestId, string $adminId, string $action, string $notes = null): array;
    public function getRequest(string $requestId): array;
    public function getUserRequests(string $userId, array $filters = []): array;
}
