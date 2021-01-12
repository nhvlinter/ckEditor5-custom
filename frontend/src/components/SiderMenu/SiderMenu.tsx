import React, { FC, CSSProperties, useMemo, useEffect, useCallback } from 'react';
import { observer, useComputed } from 'mobx-react-lite';
import { useTranslation } from 'react-i18next';

import { useStore } from '../../stores';

import { Drawer, ListItem, ListItemText, ListItemIcon, IconButton, Divider, MenuList, MenuItem, List, Collapse, useMediaQuery } from '@material-ui/core';

import { Usb, Apartment, SettingsInputComponent, Settings, Notifications, ChromeReaderMode, PermDataSetting, Category, HourglassFull, Group, FastRewind, FastForward, Adb, Announcement, Contacts, Home, Receipt, Event, ExpandLess, ExpandMore, LibraryBooks, Description, VpnKey, PermIdentity, School, HomeWork, Accessibility, MusicNote } from '@material-ui/icons';
import PersonAddDisabledIcon from '@material-ui/icons/PersonAddDisabled';
import { Link } from '../router/Links';

const styles = require("./Sider.module.scss");
import classNames from 'classnames';
import { makeStyles, Theme, createStyles, useTheme, createMuiTheme, ThemeProvider } from '@material-ui/core/styles';
import ExpansionPanel from '@material-ui/core/ExpansionPanel';
import ExpansionPanelSummary from '@material-ui/core/ExpansionPanelSummary';
import ExpansionPanelDetails from '@material-ui/core/ExpansionPanelDetails';
import Typography from '@material-ui/core/Typography';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import ChevronRightIcon from '@material-ui/icons/ChevronRight';
import TreeItem from '@material-ui/lab/TreeItem';
import TreeView from '@material-ui/lab/TreeView';
import TextField from '@material-ui/core/TextField';
import { cpus } from 'os';
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
		}
	}),
);


export const SiderMenu: FC<{ collapsed: boolean }> = observer(({ collapsed }) => {
	const [drawerWidth, setDrawerWidth] = React.useState(240);
	const classes = useStyles(drawerWidth);
	return (
		<ThemeProvider >
			<Drawer
				variant="permanent"
				className={classNames(classes.paper, styles.drawer, collapsed ? styles.drawerClose : styles.drawerOpen)}
				classes={{ paper: classNames(classes.paper, collapsed ? styles.drawerClose : styles.drawerOpen) }}
				open={!collapsed}
				transitionDuration={2000}>
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
		</ThemeProvider>)
});
