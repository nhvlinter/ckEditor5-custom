import React, { FC, CSSProperties, useMemo, useEffect, useCallback, useState } from 'react';
import { observer } from "mobx-react-lite";
import { useStore } from '../../stores';
import { TreeView, TreeItem } from '@material-ui/lab';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import ChevronRightIcon from '@material-ui/icons/ChevronRight';
import { TreeViewData } from '../../models/TreeViewData';
import { Typography, makeStyles, createStyles, Theme, Checkbox, Chip, Box, Dialog, Button, Grid, Link, Paper, TextField, Tabs, Tab } from "@material-ui/core";
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
import { OverviewStore } from '../../models/OverviewStore';
import DeleteForeverIcon from '@material-ui/icons/DeleteForever';
import DoneIcon from '@material-ui/icons/Done';
import { renderToString } from 'react-dom/server'

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
        },
        chip: {
            margin: theme.spacing(0.5),
        },
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
    const { sTreeViewData, sCKEditor, sOverview } = useStore();
    const classes = useTreeItemStyles();
    const [open, setOpen] = React.useState(false);
    const [tag, setTag] = useState("");
    const [valueTag, setValueTag] = useState(0);
    const [nodeData, setNodeData] = useState(null);

    useEffect(() => {
        sOverview.init();
    });

    const handleClickOpen = (nodeData) => {
        sOverview.init();
        if (nodeData != null) {
            setNodeData(nodeData);
        }
        setOpen(true);
    };
    const handleClose = () => {
        setOpen(false);
    };

    const handleChange = (event, newValue) => {
        setValueTag(newValue);
    };

    const updatedAttr2Server = () => {
        if(nodeData != null) {
            let dataHtml = ReactHtmlParser(sCKEditor.data, {
                transform (node) {
                    if(isEqualNode(node, nodeData)) {
                        if(sOverview.classes.length > 0) {
                            let classes = "";
                            for(let i =0; i < sOverview.classes.length; i++) {
                                classes += sOverview.classes[i] + " ";
                            }
                            node.attribs.class = classes;
                        }
                        if(sOverview.attributes.length > 0) {
                            for(let i = 0; i < sOverview.attributes.length; i++) {
                                node.attribs[sOverview.attributes[i].key] = sOverview.attributes[i].value;
                            }
                        }
                    } 
                }
            });
            let dataSaved = renderToString(dataHtml);
            sCKEditor.saveDataChanged(dataSaved);
            window.location.href = "/";
        }
    }

    function isEqualNode(oneNode, twoNode) {
        let flag = true;
        if(oneNode.children != undefined && twoNode.children == undefined) {
            return false;
        }
        if(oneNode.children == undefined && twoNode.children != undefined) {
            return false;
        }
        if(oneNode.children != undefined && twoNode.children != undefined) {
            if(oneNode.children.length != twoNode.children.length) {
                return false;
            }
        }
        if(oneNode.name != twoNode.name) {
            return false;
        }
        if(oneNode.attribs != undefined && twoNode.attribs == undefined) {
            return false;
        }
        if(oneNode.attribs == undefined && twoNode.attribs != undefined) {
            return false;
        }
        if(oneNode.attribs != undefined && twoNode.attribs != undefined) {
            let oneAttr = Object.entries(oneNode.attribs);
            let twoAttr = Object.entries(twoNode.attribs);
            if(oneAttr.length != twoAttr.length) {
                return false;
            } else {
                let count = 0;
                for(let i = 0; i < oneAttr.length; i++) {
                    for(let j = 0; j < twoAttr.length; j++) {
                        if(oneAttr[i][0] == twoAttr[j][0] && oneAttr[i][1] == twoAttr[j][1]) {
                            count ++;
                        }
                    }
                }
                if(oneAttr.length != count) {
                    return false;
                }
            }
        }
        if(oneNode.parent != undefined && twoNode.parent == undefined) {
            return false;
        }
        if(oneNode.parent == undefined && twoNode.parent != undefined) {
            return false;
        }
        // if(oneNode.parent != undefined && twoNode.parent != undefined) {
        //     return isEqualNode(oneNode.parent, twoNode.parent);
        // }
        if(oneNode.prev != undefined && twoNode.prev == undefined) {
            return false;
        }
        if(oneNode.prev == undefined && twoNode.prev != undefined) {
            return false;
        }
        // if(oneNode.prev != undefined && twoNode.prev != undefined) {
        //     return isEqualNode(oneNode.prev, twoNode.prev);
        // }
        if(oneNode.next != undefined && twoNode.next == undefined) {
            return false;
        }
        if(oneNode.next == undefined && twoNode.next != undefined) {
            return false;
        }
        // if(oneNode.next != undefined && twoNode.next != undefined) {
        //     return isEqualNode(oneNode.next, twoNode.next);
        // }
        return true;
    }

    function transform(node, index) {
        if (node.name != undefined && node.name != null) {
            return (
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
                        nodeId={index}
                        label={<div className={classes.labelRoot} >
                            {node.name}
                            <Box ml={3} />
                            <BorderColorIcon
                                onClick={() => handleClickOpen(node)}
                            />
                        </div>}
                    >
                        {node.children.length != 1 && processNodes(node.children, transform)}
                    </TreeItem>
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
            {nodeData != null && (
                <Dialog onClose={handleClose} aria-labelledby="customized-dialog-title" open={open} maxWidth='lg'>
                    <DialogTitle id="customized-dialog-title" onClose={handleClose}>
                        {"<" + nodeData.name.toUpperCase() + "/>"}
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
                        {valueTag == 0 ? <TabPanelAddClasses node={nodeData} ></TabPanelAddClasses>
                            : valueTag == 1 ? <TabPanelAddAttributes node={nodeData}></TabPanelAddAttributes>
                                : valueTag == 2 ? <TabPanelHTMLCode></TabPanelHTMLCode>
                                    : <></>
                        }

                    </DialogContent>
                    <DialogActions>
                        <Button variant="contained" autoFocus onClick={() => updatedAttr2Server()} color="primary">
                            Apply
                        </Button>
                    </DialogActions>
                </Dialog>
            )}
        </>
    )
});
export const TabPanelAddClasses: FC<{ node }> = observer(({ node }) => {
    const classes = useTreeItemStyles();
    const { sOverview } = useStore();
    useEffect(() => {
        if (node != null) {
            sOverview.getClassesFromNode(node);
        }
    }, [node]);

    const handleDelete = useCallback((x) => {
        if (sOverview.classes.length) {
            sOverview.deleteClassInNode(x);
        }
    }, [sOverview]);

    const addClass = useCallback(() => {
        sOverview.addClassInNode();
    }, [sOverview]);

    return (
        <Grid container spacing={3}>
            <Paper variant="outlined" className={classes.paperContent}>
                <Grid item xs >
                    <Box component="div" p={1.5} ></Box>
                    <Box ml={3} component="div" display="inline"></Box>
                    <Box component="div" display="inline" p={2} ml={2} bgcolor="#f2f2f2">
                        Classes:
                        </Box>
                    <TextField
                        variant="outlined"
                        label="name"
                        onChange={(e) => sOverview.set_dataClass(e.target.value)}
                        style={{ width: "550px" }}>
                    </TextField>
                    <IconButton aria-label="add"
                        className={classes.addIcon}
                        color="primary" size="medium"
                        onClick={() => addClass()}
                    >
                        <AddIcon fontSize="large" style={{ color: "white" }} />
                    </IconButton>
                </Grid>
                <Grid item xs >
                    <Box ml={3} component="div" display="inline"></Box>
                    {sOverview.classes.length > 0 && sOverview.classes.map(x => {
                        return <Chip
                            label={x}
                            onDelete={() => handleDelete(x)}
                            className={classes.chip}
                        />
                    })}
                </Grid>
            </Paper>
        </Grid>
    );
});

export const TabPanelAddAttributes: FC<{ node }> = observer(({ node }) => {
    const classes = useTreeItemStyles();
    const { sOverview } = useStore();
    useEffect(() => {
        sOverview.init().then(() => {
            if (node != null) {
                sOverview.updateAttrFromData(node);
            }
        });
    }, [node]);

    const addAttribute = useCallback(() => {
        sOverview.addAttribute();
    }, [sOverview])

    const removeAttribute = useCallback((keyValue) => {
        sOverview.removeAttribute(keyValue);
    }, [sOverview])

    const updateAttribute = useCallback((key) => {
        sOverview.updateAttribute(key);
    }, [sOverview])

    return (
        <Grid container spacing={3}>
            <Paper variant="outlined" className={classes.paperContent}>
                <Grid item xs >
                    <Box component="div" p={1.5} ></Box>
                    <Box ml={3} component="div" display="inline"></Box>
                    <Box component="div" display="inline" p={2} ml={2} bgcolor="#f2f2f2">
                        Attributes:
                        </Box>
                    <TextField variant="outlined" label="name" style={{ width: "250px" }}
                        onChange={(e) => sOverview.attribute.set_key(e.target.value)}
                    >
                    </TextField>
                    <TextField variant="outlined" label="value" style={{ width: "300px" }}
                        onChange={(e) => sOverview.attribute.set_value(e.target.value)}
                    >
                    </TextField>
                    <IconButton aria-label="add"
                        className={classes.addIcon}
                        color="primary" size="medium"
                        onClick={() => addAttribute()}
                    >
                        <AddIcon fontSize="large" style={{ color: "white" }} />
                    </IconButton>
                </Grid>
                <Grid item xs >
                    {sOverview.attributes.length > 0 &&
                        sOverview.attributes.map(x => {
                            return (<>
                                <Box component="div" p={0.5} ></Box>
                                <Box ml={5} component="div" display="inline"></Box>
                                <TextField variant="outlined" value={x.key} style={{ width: "280px" }}
                                    disabled>
                                </TextField>
                                <TextField variant="outlined" value={x.value} style={{ width: "300px" }}
                                    onChange={(e) => sOverview.attribute.set_value(e.target.value)}
                                >
                                </TextField>
                                <IconButton aria-label="done"
                                    className={classes.addIcon}
                                    color="primary" size="medium"
                                    onClick={() => updateAttribute(x.key)}
                                >
                                    <DoneIcon fontSize="large" style={{ color: "white" }} />
                                </IconButton>
                                <IconButton aria-label="remove"
                                    className={classes.addIcon}
                                    onClick={() => removeAttribute(x.key)}
                                    color="secondary" size="medium">
                                    <DeleteForeverIcon fontSize="large" style={{ color: "white" }} />
                                </IconButton>
                            </>)
                        })
                    }

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

