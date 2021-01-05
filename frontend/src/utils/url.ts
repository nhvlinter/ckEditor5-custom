
export function qs(url:URL|string, qs:{}) {
    const u = new URL(typeof url === "string" ? url : url.toJSON());
    Object.entries(qs).forEach(([key, value]) => {
        if (value === undefined) return;
        if (Array.isArray(value)) value.forEach(v => u.searchParams.set(key, String(v)))
        else u.searchParams.set(key, String(value));
    })
    return u;
}
