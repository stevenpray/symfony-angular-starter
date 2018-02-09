export enum AuthRole {
    USER              = 1,
    ADMIN             = 1 << 1 | USER,
    ALLOWED_TO_SWITCH = 1 << 2 | USER,
    SUPER_ADMIN       = USER | ADMIN | ALLOWED_TO_SWITCH,
}
