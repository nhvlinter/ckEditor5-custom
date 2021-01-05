import React, { FC } from 'react';
import { observer } from 'mobx-react-lite';

import { useStore } from '../../stores';

import { RouterView as MsrRouterView, ViewMap } from "mobx-state-router";

export const RouterView: FC<{viewMap:ViewMap}> = observer(({viewMap}) => {
    const {routerStore} = useStore();
    return (
        <MsrRouterView routerStore={routerStore} viewMap={viewMap} />
    )
});
