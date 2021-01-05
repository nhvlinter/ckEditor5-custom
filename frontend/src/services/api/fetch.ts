import {qs} from "../../utils/url";

export const apiHost = new URL(process.env.REACT_APP_API!, location.href).toJSON();

const headers: {[key:string]:string} = {
    'Content-Type': 'application/json',
    "Accept"      : "application/json",
}
export function setAuthtoken(token:string){
    if (!token) delete headers.Authorization;
    else headers.Authorization = `Bearer ${token}`;
}

interface IErrorData<T> extends Error {
    status:number
    error?:{
        statusCode  : number;
        message     : string;
        errorCode   : string;
        errorObject?: T;
    }
}

export async function aFetch<T, TError = any>(
    method:"GET"|"POST"|"PUT"|"DELETE",
    input: string,
    body ?: {},
    init?: RequestInit,
) : Promise<[(IErrorData<TError>)|undefined, T]> {
    try {
        const url = (method != "GET" || !body) ? new URL(input, apiHost) : qs(new URL(input, apiHost), body);
        const response = await fetch(url.toJSON(), {
            method,
            headers: headers,
            mode:"cors",
            ...((method != "GET" && body) ? {body: JSON.stringify(body)} : {}),
            ...init
        });
        if (response.ok) {
            try {
                const data = await response.json();
                return [undefined, data as any as T];
            } catch(e) {
                console.warn(e);
                return [undefined, null as any as T]
            }
        }

        const err = Object.assign(new Error(response.statusText || String(response.status)), {status:response.status});
        try {
            const e = await response.json();
            return [Object.assign(err, e), null as any];
        } catch {
            return [err, null as any];
        }
    } catch (err) {
        return [err, null as any]
    }
}

export async function uploadFile<T, TErrorData = any>(
    method:"GET"|"POST"|"PUT"|"DELETE",
    input: string,
    file: Blob,
    init?: RequestInit,
) : Promise<[(Error & {data?:TErrorData})|null, T]> {
    const data = new FormData();
    data.append("files", file);
    const { "Content-Type":_, ...hs }  = headers;

    try {
        const url = new URL(input, apiHost);
        const response = await fetch(url.toJSON(), {
            method,
            headers: hs,
            // credentials: 'include',
            mode:"cors",
            body:data,
            ...init
        });
        if (response.ok) {
            try {
                const {data} = await response.json();
                return [null, data as any as T];
            } catch(e) {
                console.warn(e);
                return [null, null as any as T]
            }
        }

        const {error} = await response.json();
        const err = error != null ? new Error(error.message) : new Error(response.statusText || String(response.status));

        try {
            const {data} = await response.json();
            return [Object.assign(err, {data}), null as any];
        } catch {
            return [err, null as any];
        }
    } catch (err) {
        return [err, null as any]
    }
}
