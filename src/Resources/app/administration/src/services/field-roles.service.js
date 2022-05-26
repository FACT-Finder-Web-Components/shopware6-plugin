const ApiService = Shopware.Classes.ApiService;

class FieldRolesService extends ApiService
{
    constructor(httpClient, loginService) {
        super(httpClient, loginService, null, 'application/json');
        this.name = 'fieldRolesServiceApi';
    }

    sendUpdateFieldRoles() {
        const headers = this.getBasicHeaders();
        return this.httpClient
                   .get(`_action/field-roles/update`, {headers}).then((response) => {
                       return ApiService.handleResponse(response);
                   });
    }
}

export default FieldRolesService;
