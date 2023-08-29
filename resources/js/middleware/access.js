export function checkRolesAccess(roles) {
    if (roles.length === 0) {
        return true;
    }
    let authUser = undefined;
    authUser = JSON.parse(sessionStorage.getItem('authUser'))
    if (!authUser || authUser.length === 0) {
        return false;
    }
    const userRoles = authUser.roles;
    return userRoles.some((role) => roles.includes(role.name));
}

export function checkPermissionAccess(permissions) {
    if (permissions.length === 0) {
        return true;
    }
    let authUser = undefined;
    authUser = JSON.parse(sessionStorage.getItem('authUser'))
    if (!authUser || authUser.length === 0) {
        return false;
    }
    const userRoles = authUser.roles;
    let hasAccess = false;

    for (const role of userRoles) {
        if (role.permissions.some(permission => permissions.includes(permission.name))) {
            hasAccess = true;
            break;
        }
    }
    return hasAccess;
}
