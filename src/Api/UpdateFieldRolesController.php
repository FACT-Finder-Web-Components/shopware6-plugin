<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Api;

use Exception;
use Omikron\FactFinder\Shopware6\Config\FieldRolesInterface;
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

    public function __construct(FieldRolesInterface $fieldRolesService, EntityRepositoryInterface $channelRepository)
    {
        $this->fieldRoles        = $fieldRolesService;
        $this->channelRepository = $channelRepository;
    }

    /**
     * @Route("/api/_action/field-roles/update", name="api.action.fact_finder.field_roles.update", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function execute(): JsonResponse
    {
        foreach ($this->fetchSalesChannels() as $salesChannel) {
            $fieldRoles = $this->fieldRoles->getRoles($salesChannel->getId());
            $this->fieldRoles->update($fieldRoles, $salesChannel->getId());
        }

        return new JsonResponse();
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
