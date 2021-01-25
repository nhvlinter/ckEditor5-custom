import React, { CSSProperties, useCallback } from 'react';
import { observer } from 'mobx-react-lite';

import {SiderMenuLeft } from './SiderMenuLeft';

import { useStore } from '../../stores';

const drawerStyle: CSSProperties = {
    padding: 0,
    height: '100vh',
}

export const SiderMenuWrapperLeft = observer(() => {
    const {sLeftNav} = useStore();
    return (<SiderMenuLeft collapsed={sLeftNav.collapsed} />)
});
