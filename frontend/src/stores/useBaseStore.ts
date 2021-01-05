import { Context, useContext } from "react";
import { BaseStore } from "./BaseStore";

export let BaseStoreContext: Context<BaseStore> = null as any;
export function setBaseStoreContext(context: Context<BaseStore>) {
    BaseStoreContext = context;
}

export function useBaseStore() {
    if (BaseStoreContext == null) throw new Error("required setBaseStoreContext");
    return useContext(BaseStoreContext);
}
