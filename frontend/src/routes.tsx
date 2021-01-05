import React, { ReactNode } from 'react';
import { RouterState, Route, StringMap, TransitionHook } from 'mobx-state-router';

import {mapValues, toPairs} from "lodash-es";

import { Store } from './stores/Store';

export const homeRoute = new RouterState("home");
export const notFound = new RouterState('notFound');

const checkForUserSignedIn: TransitionHook = async (fromState, toState, routerStore) => {
    const store:Store = routerStore.rootStore;
    //const isLogin = await store.checkLogin();
    const isLogin = true;
    if (!isLogin) {
        location.reload();
    }
};

import { HomePage            } from "./pages/home/HomePage";
const notFoundComp = (<div><h1>404</h1><div><a href="/">back to home</a></div></div>);

export const routeConfig: {[key:string]: {pattern:string, comp:ReactNode, allowAnonymous?:boolean}} = {
    notFound            : ({pattern:"/404"                                 , comp: (notFoundComp           ), allowAnonymous:true }),
    home                : ({pattern:"/"                                    , comp: (<HomePage            />), }),
};

export const appViewMap = mapValues(routeConfig, c => c.comp);
export const routes = toPairs(routeConfig).map<Route>(([name, c]) => ({
    name, pattern:c.pattern,
    beforeEnter: !!c.allowAnonymous ? undefined :checkForUserSignedIn
}));

function safeFromState(state: RouterState) {
    return state.routeName === "__initial__" ? state : homeRoute
}

function redirect(routeName:string, params?: StringMap, queryParams?: Object) {
    return () => Promise.reject(new RouterState(routeName, params, queryParams))
}
