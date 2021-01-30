import React, { FC, CSSProperties, useMemo, useEffect, useCallback, useState } from 'react';
import { observer } from "mobx-react-lite";
import { useStore } from '../../stores';
import { TreeView, TreeItem } from '@material-ui/lab';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import ChevronRightIcon from '@material-ui/icons/ChevronRight';
import { TreeViewData } from '../../models/TreeViewData';
import { Typography, makeStyles, createStyles, Theme, Checkbox, Box, Dialog, Button } from "@material-ui/core";
import MuiDialogTitle from '@material-ui/core/DialogTitle';
import MuiDialogContent from '@material-ui/core/DialogContent';
import MuiDialogActions from '@material-ui/core/DialogActions';
import BorderColorIcon from '@material-ui/icons/BorderColor';
import { withStyles } from '@material-ui/core/styles';
import IconButton from '@material-ui/core/IconButton';
import CloseIcon from '@material-ui/icons/Close';

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

export const Overview: FC<{ item: TreeViewData }> = observer(({ item }) => {
    const { sTreeViewData } = useStore();
    const classes = useTreeItemStyles();
    const [open, setOpen] = React.useState(false);
    const [tag, setTag] = useState("");

    const handleClickOpen = (label) => {
        setTag(label);
        setOpen(true);
    };
    const handleClose = () => {
        setOpen(false);
    };

    return (
        <>
            <TreeView
                defaultCollapseIcon={<ExpandMoreIcon />}
                defaultExpandIcon={<ChevronRightIcon />}
            >
                <TreeItem
                    classes={{
                        root: classes.root,
                        expanded: classes.expanded,
                        selected: classes.selected,
                        group: classes.group,
                        label: classes.label,
                    }}
                    nodeId={item.nodeId != null ? item.nodeId : "1"}
                    label={<div className={classes.labelRoot} >
                        {item.label}
                        <Box ml={2} />
                        <BorderColorIcon 
                            onClick={() => handleClickOpen(item.label)}
                        />
                    </div>}
                >
                    {item.child != null && (
                        <>
                            {item.child.map(i => {
                                return (<>
                                    <Overview item={i}></Overview>
                                </>)
                            })}
                        </>)}
                </TreeItem>
            </TreeView>
            <Dialog onClose={handleClose} aria-labelledby="customized-dialog-title" open={open}>
                <DialogTitle id="customized-dialog-title" onClose={handleClose}>
                    {"<" + tag.toUpperCase() + "/>"}
                </DialogTitle>
                <DialogContent dividers>
                    <Typography gutterBottom>
                        Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis
                        in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.
                    </Typography>
                    <Typography gutterBottom>
                        Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis
                        lacus vel augue laoreet rutrum faucibus dolor auctor.
                    </Typography>
                    <Typography gutterBottom>
                        Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel
                        scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus
                        auctor fringilla.
                    </Typography>
                </DialogContent>
                <DialogActions>
                    <Button autoFocus onClick={handleClose} color="primary">
                        Apply
                    </Button>
                </DialogActions>
            </Dialog>
        </>
    )
});