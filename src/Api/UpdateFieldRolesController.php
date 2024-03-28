<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Api;

use Omikron\FactFinder\Shopware6\Config\FieldRolesInterface;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['api']])]
class UpdateFieldRolesController extends AbstractController
{
    public function __construct(
        private readonly FieldRolesInterface $fieldRolesService,
        private readonly EntityRepository $channelRepository,
        private readonly LoggerInterface $factfinderLogger
    ) {
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/_action/field-roles/update', name: 'api.action.fact_finder.field_roles.update', defaults: ['XmlHttpRequest' => true], methods: ['GET'])]
    public function execute(Context $context): JsonResponse
    {
        try {
            foreach ($this->fetchSalesChannels($context) as $salesChannel) {
                $fieldRoles = $this->fieldRolesService->getRoles($salesChannel->getId());
                $this->fieldRolesService->update($fieldRoles, $salesChannel->getId());
            }

            return new JsonResponse();
        } catch (\Exception $e) {
            $this->factfinderLogger->error($e->getMessage());

            return new JsonResponse(['message' => 'Problem with update fields roles. Check logs for more informations'], 400);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function fetchSalesChannels(Context $context): EntityCollection
    {
        return $this->channelRepository->search(new Criteria(), $context)->getEntities();
    }
}
