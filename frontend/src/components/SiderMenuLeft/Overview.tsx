import React, { FC, CSSProperties, useMemo, useEffect, useCallback, useState } from 'react';
import { observer } from "mobx-react-lite";
import { useStore } from '../../stores';
import { TreeView, TreeItem } from '@material-ui/lab';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import ChevronRightIcon from '@material-ui/icons/ChevronRight';
import { TreeViewData } from '../../models/TreeViewData';
import { Typography, makeStyles, createStyles, Theme, Checkbox, Box, Dialog, Button, Grid, Link, Paper, TextField, Tabs, Tab } from "@material-ui/core";
import MuiDialogTitle from '@material-ui/core/DialogTitle';
import MuiDialogContent from '@material-ui/core/DialogContent';
import MuiDialogActions from '@material-ui/core/DialogActions';
import BorderColorIcon from '@material-ui/icons/BorderColor';
import { withStyles } from '@material-ui/core/styles';
import IconButton from '@material-ui/core/IconButton';
import CloseIcon from '@material-ui/icons/Close';
import AssistantIcon from '@material-ui/icons/Assistant';
import SettingsIcon from '@material-ui/icons/Settings';
import CodeIcon from '@material-ui/icons/Code';
import AddIcon from '@material-ui/icons/Add';
import PropTypes from 'prop-types';
import ReactHtmlParser, { processNodes, convertNodeToElement } from 'react-html-parser';

const useTreeItemStyles = makeStyles((theme: Theme) =>
    createStyles({
        root: {
            color: theme.palette.text.secondary,
            '&:hover > $content': {
                backgroundColor: theme.palette.action.hover,
            },
            '&:focus > $content, &$selected > $content': {
                backgroundColor: `var(--tree-view-bg-color, ${theme.palette.grey[400]})`,
                color: 'var(--tree-view-color)',
            },
            '&:focus > $content $label, &:hover > $content $label, &$selected > $content $label': {
                backgroundColor: 'transparent',
            },
        },
        content: {
            color: theme.palette.text.secondary,
            borderTopRightRadius: theme.spacing(2),
            borderBottomRightRadius: theme.spacing(2),
            paddingRight: theme.spacing(1),
            fontWeight: theme.typography.fontWeightMedium,
            '$expanded > &': {
                fontWeight: theme.typography.fontWeightRegular,
            },
        },
        group: {
            marginLeft: theme.spacing(2),
            '& $content': {
                paddingLeft: theme.spacing(2),
            },
        },
        expanded: {
        },
        selected: {},
        label: {
            fontWeight: 'inherit',
            color: 'inherit',
        },
        labelRoot: {
            display: 'flex',
            alignItems: 'center',
            padding: theme.spacing(0.5, 2),
        },
        labelIcon: {
            marginRight: theme.spacing(1),
        },
        labelText: {
            fontWeight: 'inherit',
            flexGrow: 1,
        },
        userlabelItem: {
            width: "300px",
            display: 'inline-table'
        },
        paperIcon: {
            padding: theme.spacing(1),
            textAlign: 'center',
            color: theme.palette.text.secondary,
        },
        paperContent: {
            width: theme.spacing(100),
            height: theme.spacing(50),
            margin: theme.spacing(2)
        },
        button: {
            margin: theme.spacing(2),
        },
        addIcon: {
            border: "1px solid black",
            marginLeft: "10px",
            backgroundColor: "blue"
        }
    }),
);

const styles = (theme) => ({
    root: {
        margin: 0,
        padding: theme.spacing(2),
    },
    closeButton: {
        position: 'absolute',
        right: theme.spacing(1),
        top: theme.spacing(1),
        color: theme.palette.grey[500],
    },

});

const DialogTitle = withStyles(styles)((props) => {
    const { children, classes, onClose, ...other } = props;
    return (
        <MuiDialogTitle disableTypography className={classes.root} {...other}>
            <Typography variant="h6">{children}</Typography>
            {onClose ? (
                <IconButton aria-label="close" className={classes.closeButton} onClick={onClose}>
                    <CloseIcon />
                </IconButton>
            ) : null}
        </MuiDialogTitle>
    );
});

const DialogContent = withStyles((theme) => ({
    root: {
        padding: theme.spacing(2),
    },
}))(MuiDialogContent);

const DialogActions = withStyles((theme) => ({
    root: {
        margin: 0,
        padding: theme.spacing(1),
    },
}))(MuiDialogActions);

export const Overview: FC<{ item: any }> = observer(({ item }) => {
    const { sTreeViewData, sCKEditor } = useStore();
    const classes = useTreeItemStyles();
    const [open, setOpen] = React.useState(false);
    const [tag, setTag] = useState("");
    const [valueTag, setValueTag] = useState(0);

    const handleClickOpen = (label) => {
        setTag(label);
        setOpen(true);
    };
    const handleClose = () => {
        setOpen(false);
    };

    const handleChange = (event, newValue) => {
        setValueTag(newValue);
    };

    function transform(node, index) {
        if (node.name != undefined && node.name != null) {
            return (
                <TreeView
                    defaultCollapseIcon={<ExpandMoreIcon />}
                    defaultExpandIcon={<ChevronRightIcon />}
                >
                    {/* {node.children.length != 1 && */}
                        <TreeItem
                            classes={{
                                root: classes.root,
                                expanded: classes.expanded,
                                selected: classes.selected,
                                group: classes.group,
                                label: classes.label,
                            }}
                            nodeId={index}
                            label={<div className={classes.labelRoot} >
                                {node.name}
                                <Box ml={3} />
                                <BorderColorIcon
                                    onClick={() => handleClickOpen(node.name)}
                                />
                            </div>}
                        >
                            {node.children.length != 1 && processNodes(node.children, transform)}
                        </TreeItem>
                    {/* } */}
                </TreeView>
            );
        }
    }

    const options = {
        decodeEntities: true,
        transform
    };

    return (
        <>
            {ReactHtmlParser(sCKEditor.data, options)}
            <Dialog onClose={handleClose} aria-labelledby="customized-dialog-title" open={open} maxWidth='lg'>
                <DialogTitle id="customized-dialog-title" onClose={handleClose}>
                    {"<" + tag.toUpperCase() + "/>"}
                </DialogTitle>
                <DialogContent dividers>
                    <Grid container spacing={3}>
                        <Tabs
                            value={valueTag}
                            onChange={handleChange}
                            variant="fullWidth"
                            indicatorColor="secondary"
                            textColor="secondary"
                            aria-label="simple tabs example">
                            <Tab
                                icon={<AssistantIcon
                                    fontSize='large'
                                    color='error'
                                />}
                                aria-label="add-class"
                                value={0}
                            />
                            <Tab
                                icon={<SettingsIcon fontSize='large'
                                    color='error' />}
                                aria-label="add-attributes"
                                value={1}
                            />
                            <Tab
                                icon={<CodeIcon fontSize='large'
                                    color='error' />}
                                aria-label="code-html"
                                value={2}
                            />
                        </Tabs>
                    </Grid>
                    {valueTag == 0 ? <TabPanelAddClasses ></TabPanelAddClasses>
                        : valueTag == 1 ? <TabPanelAddAttributes></TabPanelAddAttributes>
                            : valueTag == 2 ? <TabPanelHTMLCode></TabPanelHTMLCode>
                                : <></>
                    }

                </DialogContent>
                <DialogActions>
                    <Button variant="contained" autoFocus onClick={handleClose} color="primary">
                        Apply
                    </Button>
                </DialogActions>
            </Dialog>
        </>
    )
});
export const TabPanelAddClasses = observer(() => {
    const classes = useTreeItemStyles();
    return (
        <Grid container spacing={3}>
            <Paper variant="outlined" className={classes.paperContent}>
                <Grid item xs >
                    <Box component="div" p={1.5} ></Box>
                    <Box ml={3} component="div" display="inline"></Box>
                    <Box component="div" display="inline" p={2} ml={2} bgcolor="#f2f2f2">
                        Classes:
                        </Box>
                    <TextField variant="outlined" label="name" style={{ width: "550px" }}>
                    </TextField>
                    <IconButton aria-label="add"
                        className={classes.addIcon}
                        color="primary" size="medium">
                        <AddIcon fontSize="large" style={{ color: "white" }} />
                    </IconButton>
                </Grid>
            </Paper>
        </Grid>
    );
});

export const TabPanelAddAttributes = observer(() => {
    const classes = useTreeItemStyles();
    return (
        <Grid container spacing={3}>
            <Paper variant="outlined" className={classes.paperContent}>
                <Grid item xs >
                    <Box component="div" p={1.5} ></Box>
                    <Box ml={3} component="div" display="inline"></Box>
                    <Box component="div" display="inline" p={2} ml={2} bgcolor="#f2f2f2">
                        Attributes:
                        </Box>
                    <TextField variant="outlined" label="name" style={{ width: "250px" }}>
                    </TextField>
                    <TextField variant="outlined" label="value" style={{ width: "300px" }}>
                    </TextField>
                    <IconButton aria-label="add"
                        className={classes.addIcon}
                        color="primary" size="medium">
                        <AddIcon fontSize="large" style={{ color: "white" }} />
                    </IconButton>
                </Grid>
            </Paper>
        </Grid>
    );
});

export const TabPanelHTMLCode = observer(() => {
    const classes = useTreeItemStyles();
    return (
        <Grid container spacing={3}>
            <Paper variant="outlined" className={classes.paperContent} disabled>
            </Paper>
        </Grid>
    );
});

