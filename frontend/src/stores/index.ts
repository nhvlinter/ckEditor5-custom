import { createContext, useContext, Context } from "react";
import { Store } from "./Store";


export const store = new Store();
export const StoreContext = createContext(store);

import {setBaseStoreContext} from "./useBaseStore";
setBaseStoreContext(StoreContext as Context<any>);

export function useStore() {
    return useContext(StoreContext);
}

// @ts-ignore
window.store = store;
