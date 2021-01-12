import React, { CSSProperties, useCallback } from 'react';
import { observer } from 'mobx-react-lite';

import { ProfileSiderMenu, SiderMenu } from './SiderMenu';

import { useStore } from '../../stores';

const drawerStyle: CSSProperties = {
    padding: 0,
    height: '100vh',
}

export const SiderMenuWrapper = observer(() => {
    const {sLeftNav} = useStore();
    return (<SiderMenu collapsed={sLeftNav.collapsed} />)
});
