import * as debug from 'debug';
import {AuthRole} from './auth-role';

const log = debug('app:auth');

export class AuthUser {

    private _id: string;
    private _role: AuthRole;

    /**
     * @param {string} id
     * @param {AuthRole} role
     */
    constructor(id: string, role: AuthRole) {
        this._id = id;
        this._role = role;

        log('Access token has roles.', AuthUser._getRoles(this._role));
    }

    /**
     * @returns {string}
     */
    public get id(): string {
        return this._id;
    }

    /**
     * @param {AuthRole} role
     * @returns {boolean}
     */
    public hasRole(role: AuthRole): boolean {
        return (this._role & role) === role;
    }

    /**
     * @param {AuthRole} role
     * @private
     */
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
