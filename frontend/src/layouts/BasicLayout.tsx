import React, { FC, CSSProperties } from 'react';
import { observer } from 'mobx-react-lite';

import { SiderMenuWrapperLeft } from '../components/SiderMenuLeft';
import { SiderMenuWrapperRight } from '../components/SiderMenuRight';
import styles from './BasicLayout.module.scss';
import { MuiThemeProvider, createMuiTheme } from '@material-ui/core/styles';
import { autorun } from 'mobx';
import { useMediaQuery } from '@material-ui/core';
import { useStore } from '../stores';
const theme = createMuiTheme({
    typography: {
        "fontFamily": `"Times New Roman", "Times", serif`,
        "fontSize": 14,
        "fontWeightLight": 300,
        "fontWeightRegular": 400,
        "fontWeightMedium": 500
    },
    palette: {
        primary: {
            main: '#1a237e',
        },
        secondary: {
            main: '#f44336',
        },
    },
});
export const BasicLayout: FC = observer(({ children }) => {
    const matches = useMediaQuery('(max-width:600px)');


    window.addEventListener("dragover", function (e) {
        e = e || event;
        e.preventDefault();
    }, false);
    window.addEventListener("drop", function (e) {
        e = e || event;
        e.preventDefault();
    }, false);
    return (
        <MuiThemeProvider theme={theme}>
            <div className={styles.root} style={matches ? { overflowX: "scroll", overflowY: "hidden" } : { overflowX: "hidden", overflowY: "hidden" }}>
                <SiderMenuWrapperLeft />
                <main className={styles.content}>
                    {children}
                </main>
                <SiderMenuWrapperRight />
            </div>
        </MuiThemeProvider>
    )
});
