import {InjectionToken} from '@angular/core';

export interface AuthConfig {
    urls: {
        endpoint: string;
        redirects: {
            unauthenticated: string;
            unauthorized: string;
        }
    };
}

export const AUTH_CONFIG = new InjectionToken<AuthConfig>('AUTH_CONFIG');
