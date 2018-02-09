export enum AuthRole {
    USER              = 1,
    USER_ADMIN        = 1 << 1 | USER,
    SUPER_USER        = 1 << 2 | USER,
    ALLOWED_TO_SWITCH = 1 << 3 | USER,
    SUPER_ADMIN       = SUPER_USER | ALLOWED_TO_SWITCH | USER_ADMIN,
}
