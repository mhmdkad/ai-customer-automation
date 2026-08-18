<?php

namespace App\Controller;

use App\DTO\SupportRequestInput;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\SupportRequest;

class SupportRequestController
{
    #[Route('/api/support-requests', methods: ['POST'])]
    public function create(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $data = $request->toArray();

        $input = new SupportRequestInput(
            customerEmail: $data['customerEmail'] ?? '',
            message: $data['message'] ?? '',
        );

        $violations = $validator->validate($input);

        $supportRequest = new SupportRequest();

        $supportRequest->setCustomerEmail($input->customerEmail);
        $supportRequest->setMessage($input->message);
        $supportRequest->setStatus('new');
        $supportRequest->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($supportRequest);
        $entityManager->flush();
        $supportRequest->getId();

        if (count($violations) > 0) {
            $errors = [];

            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }

            return new JsonResponse([
                'errors' => $errors,
            ], 400);
        }

        return new JsonResponse([
            'id' => $supportRequest->getId(),
            'customerEmail' => $input->customerEmail,
            'message' => $input->message,
        ], 201);



    }
}
