import React, { FC, CSSProperties, useMemo, useEffect, useCallback } from 'react';
import { observer, useComputed } from 'mobx-react-lite';
import { useTranslation } from 'react-i18next';
import { useStore } from '../../stores';
import { Drawer, AppBar, Button, Toolbar, IconButton, CssBaseline, ListItem, ListItemText, ListItemIcon, Divider, MenuList, MenuItem, List, Collapse, useMediaQuery } from '@material-ui/core';
import { Usb, Apartment, SettingsInputComponent, Settings, Notifications, ChromeReaderMode, PermDataSetting, Category, HourglassFull, Group, FastRewind, FastForward, Adb, Announcement, Contacts, Home, Receipt, Event, ExpandLess, ExpandMore, LibraryBooks, Description, VpnKey, PermIdentity, School, HomeWork, Accessibility, MusicNote } from '@material-ui/icons';
import PersonAddDisabledIcon from '@material-ui/icons/PersonAddDisabled';
import { Link } from '../router/Links';
const styles = require("./SiderLeft.module.scss");
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
import { Overview } from './Overview';
import ReactHtmlParser, { processNodes, convertNodeToElement } from 'react-html-parser';
import { TreeViewData } from "../../models/TreeViewData";

const drawerWidth = 260;
const drawerHeight = 350;

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
			// width: "5px",
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
			width: drawerWidth,
			flexShrink: 0,
			whiteSpace: 'nowrap',
			height: drawerHeight,
		},
		drawerOpen: {
			width: drawerWidth,
			height: drawerHeight,
			transition: theme.transitions.create('width', {
				easing: theme.transitions.easing.sharp,
				duration: theme.transitions.duration.enteringScreen,
			}),
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
		},
		drawerOverview: {
			width: drawerWidth,
			flexShrink: 0,
			whiteSpace: 'nowrap',
			height: `calc(100% - ${drawerHeight}px)`,
		},
		drawerOpenOverview: {
			width: drawerWidth,
			transition: theme.transitions.create('width', {
				easing: theme.transitions.easing.sharp,
				duration: theme.transitions.duration.enteringScreen,
			}),
			height: `calc(100% - ${drawerHeight}px)`,
		},
		drawerCloseOverview: {
			transition: theme.transitions.create('width', {
				easing: theme.transitions.easing.sharp,
				duration: theme.transitions.duration.leavingScreen,
			}),
			overflowX: 'hidden',
			width: theme.spacing(7) + 1,
			[theme.breakpoints.up('sm')]: {
				width: theme.spacing(0) + 1,
			},
		},
	}),
);


export const SiderMenuLeft: FC<{ collapsed: boolean }> = observer(({ collapsed }) => {
	const [drawerWidth, setDrawerWidth] = React.useState(240);
	const classes = useStyles(drawerWidth);
	const theme = useTheme();
	const [valueTab, setValueTab] = React.useState(0);
	const [open, setOpen] = React.useState(true);
	const { sTreeViewData } = useStore();
	const handleTabChange = (event, newValue) => {
		setValueTab(newValue);
	};

	useEffect(() => {
		sTreeViewData.initTreeView().then(() => {
		})
    });

	const handleDrawerClose = () => {
		setOpen(false);
	};

	const handleDrawerOpen = () => {
		setOpen(true);
	};

	return (
		<ThemeProvider >
			<div>
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
					// open={false}
					transitionDuration={2000}>
					<Tabs
						classes={{ root: classes.tabRoots }}
						value={valueTab}
						onChange={handleTabChange}
						indicatorColor="primary"
						textColor="primary"
					>
						<Tab label="Studio" classes={{ root: classes.tab }} >
						</Tab>
						<Tab label="Outline" classes={{ root: classes.tab }}>
						</Tab>
					</Tabs>
					<Divider />
					<TextField id="outlined-basic" placeholder="Search components" variant="outlined" />
					<TreeView
						className={classes.root}
						defaultCollapseIcon={<ExpandMoreIcon />}
						defaultExpandIcon={<ChevronRightIcon />}
					>
						<TreeItem nodeId="1" label="UI">
							<TreeItem nodeId="2" label="Articles" />
							<TreeItem nodeId="3" label="Features" />
							<TreeItem nodeId="4" label="Footers" />
						</TreeItem>
					</TreeView>
				</Drawer>
				<Drawer
					variant="permanent"
					className={clsx(classes.drawerOverview, {
						[classes.drawerOpenOverview]: open,
						[classes.drawerCloseOverview]: !open,
					})}
					classes={{
						paper: clsx({
							[classes.drawerOpenOverview]: open,
							[classes.drawerCloseOverview]: !open,
						}),
					}}
					// open={false}
					anchor="bottom"
					transitionDuration={2000}>
					<Divider />
					<Tabs
						classes={{ root: classes.tabRoots }}
						value={valueTab}
						onChange={handleTabChange}
						indicatorColor="primary"
						textColor="primary"
					>
						<Tab label="Overview" classes={{ root: classes.tab }}>
						</Tab>
					</Tabs>
					<Divider />
					<Overview item={sTreeViewData.data} />
				</Drawer>
			</div>
			<div >
				<IconButton onClick={open ? handleDrawerClose : handleDrawerOpen}>
					{open ? < ChevronLeftIcon /> : <ChevronRightIcon />}
				</IconButton>
			</div>
		</ThemeProvider>)
});
