import {log} from './auth.module';

export enum AuthRole {
    USER              = 1,
    ADMIN             = 1 << 1 | USER,
    ALLOWED_TO_SWITCH = 1 << 2 | USER,
    SUPER_ADMIN       = USER | ADMIN | ALLOWED_TO_SWITCH,
}

export interface AuthTokenDecoded {
    exp: number;
    iat: number;
    username: string;
    roles: number;
}

export class AuthToken {

    private _decoded: AuthTokenDecoded;
    private _encoded: string;

    constructor(encoded: string) {
        try {
            this._encoded = encoded;
            this._decoded = JSON.parse(atob(encoded.split('.')[1].replace('-', '+').replace('_', '/')));
            log('Access token has roles.', AuthToken._getRoles(this._decoded.roles));
        } catch (error) {
            throw Error('Token is malformed.');
        }
    }

    public get decoded(): AuthTokenDecoded {
        return this._decoded;
    }

    public get encoded(): string {
        return this._encoded;
    }

    public get ttl(): number {
        return this._decoded.exp - this._decoded.iat;
    }

    public get username(): string {
        return this._decoded.username;
    }

    public get expiration(): Date {
        return new Date(this._decoded.exp * 1000);
    }

    public get isExpired(): boolean {
        return this.expiration <= new Date();
    }

    public hasRole(role: AuthRole): boolean {
        return (this._decoded.roles & role) === role;
    }

    private static _getRoles(role: AuthRole): { [name: string]: number } {
        const roles: { [name: string]: number } = {};
        Object.keys(AuthRole).forEach(key => {
            if (isNaN(Number(key))) {
                const value: number = AuthRole[key];
                if (role) {
                    if ((role & value) === value) {
                        roles[key] = value;
                    }
                } else {
                    roles[key] = value;
                }
            }
        });
        return roles;
    }
}
