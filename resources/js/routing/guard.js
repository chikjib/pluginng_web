export function guardNavigation(to, auth) {
    const protectedRoute = to.matched.some(record => record.meta.requiresAuth || record.meta.requiresAdmin);
    if (protectedRoute && (!auth.check() || !auth.user)) {
        return { path: '/login', query: { redirect: to.fullPath } };
    }
    if (to.matched.some(record => record.meta.requiresAdmin) && Number(auth.user?.role) !== 1) {
        return { path: '/dashboard' };
    }
    return true;
}
