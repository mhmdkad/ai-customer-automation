<?php

namespace App\Controller;

use App\DTO\SupportRequestInput;
use App\DTO\SupportRequestStatusInput;
use App\Enum\SupportRequestStatus;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SupportRequestRepository;
use App\Service\SupportRequestService;

class SupportRequestController
{
    #[Route('/api/support-requests', methods: ['POST'])]
    public function create(
        Request $request,
        ValidatorInterface $validator,
        SupportRequestService $supportRequestService,
    ): JsonResponse {
        $data = $request->toArray();

        $input = new SupportRequestInput(
            customerEmail: $data['customerEmail'] ?? '',
            message: $data['message'] ?? '',
        );

        $violations = $validator->validate($input);

        if (count($violations) > 0) {
            $errors = [];

            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }

            return new JsonResponse([
                'errors' => $errors,
            ], 422);
        }

        $supportRequest = $supportRequestService->create(
            $input->customerEmail,
            $input->message,
        );

        return new JsonResponse([
            'id' => $supportRequest->getId(),
            'customerEmail' => $input->customerEmail,
            'message' => $input->message,
        ], 201);
    }

    #[Route('/api/support-requests/{id}', methods: ['GET'])]
    public function getOne(
        int $id,
        SupportRequestRepository $repository,
    ): JsonResponse {
        $supportRequest = $repository->find($id);

        if ($supportRequest === null) {
            return new JsonResponse([
                'error' => 'Support request not found',
            ], 404);
        }

        return new JsonResponse([
            'id' => $supportRequest->getId(),
            'customerEmail' => $supportRequest->getCustomerEmail(),
            'message' => $supportRequest->getMessage(),
            'status' => $supportRequest->getStatus(),
        ]);
    }

    #[Route('/api/support-requests', methods: ['GET'])]
    public function getByStatus(
        Request $request,
        SupportRequestRepository $repository,
    ): JsonResponse {
        $status = $request->query->get('status', 'new');

        $supportRequests = $repository->findByStatus($status);

        $results = [];

        foreach ($supportRequests as $supportRequest) {
            $results[] = [
                'id' => $supportRequest->getId(),
                'customerEmail' => $supportRequest->getCustomerEmail(),
                'message' => $supportRequest->getMessage(),
                'status' => $supportRequest->getStatus(),
            ];
        }

        return new JsonResponse($results);
    }

    #[Route('/api/support-requests/{id}', methods: ['PATCH'])]
    public function updateStatus(
        int $id,
        Request $request,
        ValidatorInterface $validator,
        SupportRequestRepository $repository,
        SupportRequestService $supportRequestService,
    ): JsonResponse {
        $data = $request->toArray();

        $input = new SupportRequestStatusInput(
            status: $data['status'] ?? '',
        );

        $violations = $validator->validate($input);

        if (count($violations) > 0) {
            $errors = [];

            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }

            return new JsonResponse([
                'errors' => $errors,
            ], 422);
        }

        $supportRequest = $repository->find($id);

        if ($supportRequest === null) {
            return new JsonResponse([
                'error' => 'Support request not found',
            ], 404);
        }

        $status = SupportRequestStatus::from($input->status);

        $supportRequest = $supportRequestService->updateStatus(
            $supportRequest,
            $status,
        );

        return new JsonResponse([
            'id' => $supportRequest->getId(),
            'customerEmail' => $supportRequest->getCustomerEmail(),
            'message' => $supportRequest->getMessage(),
            'status' => $supportRequest->getStatus(),
        ], 200);
    }
}
