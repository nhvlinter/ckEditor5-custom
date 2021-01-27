import React, { FC, CSSProperties, useMemo, useEffect, useCallback } from 'react';
import { observer, useComputed } from 'mobx-react-lite';
import { useTranslation } from 'react-i18next';

import { useStore } from '../../stores';

import { Drawer, Box, AppBar, Button, Toolbar, IconButton, CssBaseline, ListItem, ListItemText, ListItemIcon, Divider, MenuList, MenuItem, List, Collapse, useMediaQuery } from '@material-ui/core';

import { Usb, Apartment, SettingsInputComponent, Settings, Notifications, ChromeReaderMode, PermDataSetting, Category, HourglassFull, Group, FastRewind, FastForward, Adb, Announcement, Contacts, Home, Receipt, Event, ExpandLess, ExpandMore, LibraryBooks, Description, VpnKey, PermIdentity, School, HomeWork, Accessibility, MusicNote } from '@material-ui/icons';
import PersonAddDisabledIcon from '@material-ui/icons/PersonAddDisabled';
import { Link } from '../router/Links';

const styles = require("./SiderBottom.module.scss");
import classNames from 'classnames';
import { makeStyles, Theme, createStyles, useTheme, createMuiTheme, ThemeProvider } from '@material-ui/core/styles';
import ExpansionPanel from '@material-ui/core/ExpansionPanel';
import ExpansionPanelSummary from '@material-ui/core/ExpansionPanelSummary';
import ExpansionPanelDetails from '@material-ui/core/ExpansionPanelDetails';
import Typography from '@material-ui/core/Typography';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import TreeItem from '@material-ui/lab/TreeItem';
import TreeView from '@material-ui/lab/TreeView';
import TextField from '@material-ui/core/TextField';
import Tabs from '@material-ui/core/Tabs';
import Tab from '@material-ui/core/Tab';
import ChevronLeftIcon from '@material-ui/icons/ChevronLeft';
import ChevronRightIcon from '@material-ui/icons/ChevronRight';
import MenuIcon from '@material-ui/icons/Menu';
import { cpus } from 'os';
import clsx from 'clsx';

const drawerWidth = 1000;

const useStyles = makeStyles((theme: Theme) =>
    createStyles({
        root: {
            width: '100%',
            maxWidth: 360,
            backgroundColor: theme.palette.background.paper,

        },
        nested: {
            paddingLeft: theme.spacing(4),
        },
        customExpandDetail: {
            padding: 0
        },
        customListItem: {
            padding: '16px'
        },
        customExpanSummary: {
            alignItems: 'flex-start',
            justifyContent: 'flex-start'
        },
        paper: {
            width: (props) => `${props}px`
        },
        dragger: {
            width: "5px",
            cursor: "ew-resize",
            padding: "4px 0 0",
            borderTop: "1px solid #ddd",
            position: "absolute",
            top: 0,
            right: 0,
            bottom: 0,
            zIndex: 100,
            backgroundColor: "#f4f7f9"
        },
        expandPannelCustom: {
            '&::before': {
                height: '0px'
            }
        },
        tabRoots: {
            minHeight: "10px",
            height: "40px",
        },
        tab: {
            minWidth: "0px",
            width: "80px",
            textTransform: 'none',
        },
        menuButton: {
            marginRight: theme.spacing(2),
        },
        hide: {
            display: 'none',
        },
        appBar: {
            transition: theme.transitions.create(['margin', 'width'], {
                easing: theme.transitions.easing.sharp,
                duration: theme.transitions.duration.leavingScreen,
            }),
        },
        appBarShift: {
            width: `calc(100% - ${drawerWidth}px)`,
            marginLeft: drawerWidth,
            transition: theme.transitions.create(['margin', 'width'], {
                easing: theme.transitions.easing.easeOut,
                duration: theme.transitions.duration.enteringScreen,
            }),
        },
        drawer: {
            width: '100%',
            flexShrink: 0,
            whiteSpace: 'nowrap',
        },
        drawerOpen: {
            width: '100%',
            transition: theme.transitions.create('width', {
                easing: theme.transitions.easing.sharp,
                duration: theme.transitions.duration.enteringScreen,
            }),
            marginLeft: '240px'
        },
        drawerClose: {
            transition: theme.transitions.create('width', {
                easing: theme.transitions.easing.sharp,
                duration: theme.transitions.duration.leavingScreen,
            }),
            overflowX: 'hidden',
            width: theme.spacing(7) + 1,
            [theme.breakpoints.up('sm')]: {
                width: theme.spacing(0) + 1,
            },
            marginLeft: '240px'
        },
        notPadingMargin: {
            padding: '0px',
            margin: '0px'
        },
    }),
);


export const SiderMenuBottom: FC<{ collapsed: boolean }> = observer(({ collapsed }) => {
    const [drawerWidth, setDrawerWidth] = React.useState(240);
    const classes = useStyles(drawerWidth);
    const theme = useTheme();
    const [valueTab, setValueTab] = React.useState(0);
    const [open, setOpen] = React.useState(true);
    const handleTabChange = (event, newValue) => {
        setValueTab(newValue);
    };

    const handleDrawerClose = () => {
        setOpen(false);
    };

    const handleDrawerOpen = () => {
        setOpen(true);
    };

    return (
        <ThemeProvider >
            <Drawer
                variant="permanent"
                className={clsx(classes.drawer, {
                    [classes.drawerOpen]: open,
                    [classes.drawerClose]: !open,
                })}
                classes={{
                    paper: clsx({
                        [classes.drawerOpen]: open,
                        [classes.drawerClose]: !open,
                    }),
                }}
                anchor="bottom"
                // open={false}
                transitionDuration={2000}
            >
                <Tabs
                    classes={{ root: classes.tabRoots }}
                    value={valueTab}
                    onChange={handleTabChange}
                    indicatorColor="primary"
                    textColor="primary"
                >
                    <Tab label="HTML" classes={{ root: classes.tab }} >
                    </Tab>
                </Tabs>
                <TextField id="outlined-basic" placeholder="Find element by text" variant="outlined" />
                <Divider />
            </Drawer>
        </ThemeProvider>)
});
