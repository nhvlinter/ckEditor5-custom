import React, { AnchorHTMLAttributes, FC } from 'react';
import { observer } from 'mobx-react-lite';

import { useStore } from '../../stores';

import { StringMap, RouterState, Link as MsrLink } from "mobx-state-router";

interface ILinkProps extends AnchorHTMLAttributes<HTMLAnchorElement> {
    routeName       : string;
    params         ?: StringMap;
    queryParams    ?: Object;
    activeClassName?: string;
}

export const Link: FC<ILinkProps> = observer(({routeName, params, queryParams, children, ...props}) => {
    const {routerStore} = useStore();
    return (
        <MsrLink {...props} routerStore={routerStore} toState={new RouterState(routeName, params, queryParams)}>
            {children}
        </MsrLink>
    )
});
