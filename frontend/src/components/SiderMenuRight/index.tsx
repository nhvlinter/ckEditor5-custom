import React, { CSSProperties, useCallback } from 'react';
import { observer } from 'mobx-react-lite';

import {SiderMenuRight } from './SiderMenuRight';

import { useStore } from '../../stores';

const drawerStyle: CSSProperties = {
    padding: 0,
    height: '100vh',
}

export const SiderMenuWrapperRight = observer(() => {
    const {sLeftNav} = useStore();
    return (<SiderMenuRight collapsed={sLeftNav.collapsed} />)
});
