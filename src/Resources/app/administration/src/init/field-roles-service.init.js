import FieldRolesService from '../services/field-roles.service'

Shopware.Service().register('fieldRolesService', () => {
    return new FieldRolesService(Shopware.Application.getContainer('init').httpClient, Shopware.Service('loginService'));
});
