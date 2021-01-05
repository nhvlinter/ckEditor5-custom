import React, { FC, ReactNode, ReactElement, useEffect } from 'react';
import { observer } from 'mobx-react-lite';
import { useStore } from '../../stores';

import styles from "./HomePage.module.scss";

export const HomePage: FC<{}> = observer(({}) => {
    //const {currentUser} = useStore();
    const { routerStore } = useStore();
    return (<div style={{ maxWidth: "100%" }}>
        Hello
        </div>);
});




