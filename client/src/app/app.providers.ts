import {MetaLoader, MetaSettings, MetaStaticLoader, PageTitlePositioning} from '@ngx-meta/core';

export function appMetaProviderFactory(): MetaLoader {
    const settings: MetaSettings = {
        pageTitlePositioning: PageTitlePositioning.PrependPageTitle,
        pageTitleSeparator: ' — ',
        applicationName: 'Symfony-Angular Starter',
        defaults: {
            'og:type': 'website',
            'og:locale': 'en_US',
        },
    };
    return new MetaStaticLoader(settings);
}

export const appMetaProvider = {
    provide: MetaLoader,
    useFactory: (appMetaProviderFactory),
};
