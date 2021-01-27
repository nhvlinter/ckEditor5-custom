import React, { CSSProperties, useCallback } from 'react';
import { observer } from 'mobx-react-lite';

import {SiderMenuBottom } from './SiderMenuBottom';

import { useStore } from '../../stores';

const drawerStyle: CSSProperties = {
    padding: 0,
    height: '100vh',
}

export const SiderMenuWrapperBottom = observer(() => {
    const {sLeftNav} = useStore();
    return (<SiderMenuBottom collapsed={sLeftNav.collapsed} />)
});
