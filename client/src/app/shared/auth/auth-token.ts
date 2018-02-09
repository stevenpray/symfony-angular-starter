import {AuthUser} from './auth-user';

export interface AuthTokenDecoded {
    exp: number;
    iat: number;
    jti: string;
    user: string;
    role: number;
};

export class AuthToken {

    private _decoded: AuthTokenDecoded;
    private _encoded: string;
    private _user: AuthUser;

    /**
     * @param {string} encoded
     */
    constructor(encoded: string) {
        try {
            this._decoded = JSON.parse(atob(encoded.split('.')[1].replace('-', '+').replace('_', '/')));
            this._encoded = encoded;
            this._user = new AuthUser(this._decoded.user, this._decoded.role);
        } catch (error) {
            throw Error('Token is malformed.');
        }
    }

    public get decoded(): AuthTokenDecoded {
        return this._decoded;
    }

    /**
     * @returns {string}
     */
    public get encoded(): string {
        return this._encoded;
    }

    /**
     * @returns {Date}
     */
    public get expiration(): Date {
        return new Date(this._decoded.exp * 1000);
    }

    /**
     * @returns {boolean}
     */
    public get expired(): boolean {
        return this.expiration <= new Date();
    }

    /**
     * @returns {number}
     */
    public get ttl(): number {
        return this._decoded.exp - this._decoded.iat;
    }

    /**
     * @returns {AuthUser}
     */
    public get user(): AuthUser {
        return this._user;
    }
}
