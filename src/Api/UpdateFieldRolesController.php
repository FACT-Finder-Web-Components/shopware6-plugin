<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Api;

use Omikron\FactFinder\Shopware6\Config\FieldRolesInterface;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class UpdateFieldRolesController extends AbstractController
{
    private FieldRolesInterface $fieldRoles;
    private EntityRepositoryInterface $channelRepository;
    private LoggerInterface $factfinderLogger;

    public function __construct(
        FieldRolesInterface $fieldRolesService,
        EntityRepositoryInterface $channelRepository,
        LoggerInterface $factfinderLogger
    ) {
        $this->fieldRoles        = $fieldRolesService;
        $this->channelRepository = $channelRepository;
        $this->factfinderLogger  = $factfinderLogger;
    }

    /**
     * @Route("/api/_action/field-roles/update", name="api.action.fact_finder.field_roles.update", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function execute(): JsonResponse
    {
        try {
            foreach ($this->fetchSalesChannels() as $salesChannel) {
                $fieldRoles = $this->fieldRoles->getRoles($salesChannel->getId());
                $this->fieldRoles->update($fieldRoles, $salesChannel->getId());
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
    private function fetchSalesChannels(): EntityCollection
    {
        $context = Context::createDefaultContext();

        return $this->channelRepository->search(new Criteria(), $context)->getEntities();
    }
}
