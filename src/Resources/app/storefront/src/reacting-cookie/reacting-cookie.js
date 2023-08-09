import { COOKIE_CONFIGURATION_UPDATE } from 'src/plugin/cookie/cookie-configuration.plugin';

document.$emitter.subscribe(COOKIE_CONFIGURATION_UPDATE, eventCallback);

function eventCallback(updatedCookies) {
    const trackingCookies = ['ff_has_just_logged_in', 'ff_has_just_logged_out', 'ff_user_id'];

    trackingCookies.forEach(function (cookieName) {
        console.log(cookieName);
        if (typeof updatedCookies.detail[cookieName] !== 'undefined') {
            if (updatedCookies.detail[cookieName]) {
                document.cookie = cookieName+'=0';
            } else {
                document.cookie = cookieName+'=; Max-Age=-99999999;';
            }
        }
    });
}
