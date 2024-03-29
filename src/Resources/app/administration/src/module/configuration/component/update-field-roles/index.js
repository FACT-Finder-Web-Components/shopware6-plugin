import template from './update-field-roles.html.twig';

const {Component, Mixin} = Shopware;

Component.register('update-field-roles', {
        template,
        inject: ['fieldRolesService',],
        mixins: [
            Mixin.getByName('notification'),
            Mixin.getByName('sw-inline-snippet'),
        ],
        data() {
            return {
                isLoading: false,
                isSaveSuccessful: false,
            };
        },

        methods: {
            async onClick() {
                this.isLoading = true;
                const response = await this.fieldRolesService.sendUpdateFieldRoles();
                this.isSaveSuccessful = true;
                this.isLoading = false;
                this.createNotificationSuccess({
                    message: Shopware.Snippet.tc('configuration.updateFieldRoles.update')
                })
            },
        },
    },
);
