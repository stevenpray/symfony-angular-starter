import {MetaLoader, MetaSettings, MetaStaticLoader, PageTitlePositioning} from '@ngx-meta/core';
import {environment} from '../environments/environment';

export function appMetaProviderFactory(): MetaLoader {
    const settings: MetaSettings = {
        pageTitlePositioning: PageTitlePositioning.PrependPageTitle,
        pageTitleSeparator: ' — ',
        applicationName: 'Symfony-Angular Starter',
        defaults: {
            'og:type': 'website', 'og:locale': 'en_US',
        },
    };
    return new MetaStaticLoader(settings);
}

export const metaConfig = {
    provide: MetaLoader, useFactory: (appMetaProviderFactory),
};

export const authConfig = {
    urls: {
        endpoint: environment.urls.api, redirects: {
            unauthenticated: 'signin',
            unauthorized: 'signin',
        },
    },
};

export function restangularConfig(provider) {
    provider.setBaseUrl(environment.urls.api);
}

