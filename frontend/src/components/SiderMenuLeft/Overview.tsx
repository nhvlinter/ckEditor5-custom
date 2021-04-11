import React, { FC, CSSProperties, useMemo, useEffect, useCallback, useState } from 'react';
import { observer } from "mobx-react-lite";
import { useStore } from '../../stores';
import { TreeView, TreeItem } from '@material-ui/lab';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import ChevronRightIcon from '@material-ui/icons/ChevronRight';
import { TagData } from '../../models/TagData';
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
import { useDrag, useDrop, DropTargetMonitor } from "react-dnd";

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
        paperContentCode: {
            width: theme.spacing(100),
            // height: theme.spacing(50),
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
    const { sTreeViewData, sCKEditor, sOverview, routerStore } = useStore();
    const classes = useTreeItemStyles();
    const [open, setOpen] = React.useState(false);
    const [tag, setTag] = useState("");
    const [valueTag, setValueTag] = useState(0);
    const [nodeData, setNodeData] = useState(null);
    const [expandedId, setExpandedId] = useState([]);

    useEffect(() => {
        sCKEditor.init();
        setExpandedId(sCKEditor.reactIds);
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
        if (nodeData != null) {
            let reactIdNodeData = nodeData.id;
            let dataHtml = ReactHtmlParser(sCKEditor.data, {
                transform(node) {
                    if (node.name != undefined && node.name != null) {
                        let reactIdNode = node.attribs.reactid;
                        if (reactIdNode == reactIdNodeData) {
                            if (sOverview.classes.length > 0) {
                                let classes = "";
                                for (let i = 0; i < sOverview.classes.length; i++) {
                                    classes += sOverview.classes[i] + " ";
                                }
                                node.attribs.class = classes;
                            }
                            if (sOverview.attributes.length > 0) {
                                for (let i = 0; i < sOverview.attributes.length; i++) {
                                    if (!(sOverview.attributes[i].key == 'id' && sOverview.attributes[i].value == '')) {
                                        node.attribs[sOverview.attributes[i].key] = sOverview.attributes[i].value;
                                    }
                                }
                            }
                        }
                    }

                }
            });
            let dataSaved = renderToString(dataHtml);
            sCKEditor.saveDataChanged(dataSaved);
            setOpen(false);
        }
    }

    const handledLabelTreeViewClick = useCallback((e, reactId) => {
        e.preventDefault();
        sCKEditor.set_reactId(reactId);
    }, [sCKEditor]);

    const handledOnclickTreeView = useCallback((e, item) => {
        e.preventDefault();
        sCKEditor.removeOrAddEleFromReactIds(item);
        let temp: string[] = [];
        sCKEditor.reactIds.map(x => temp.push(x));
        setExpandedId(temp);
    }, [expandedId]);

    let flagRemove = false;

    function removeElement(cardArray, id) {
        if (cardArray.length > 0) {
            for (let i = 0; !flagRemove && i < cardArray.length; i++) {
                if (cardArray[i] != null && cardArray[i].id == id) {
                    cardArray.splice(i, 1);
                    flagRemove = true;
                } else {
                    if (cardArray[i] != null && cardArray[i].children != null && cardArray[i].children != undefined && !flagRemove) {
                        removeElement(cardArray[i].children, id);
                    }
                }
            }
        }
    }

    let flagAdd = false;

    function addElement(cardArray, card, idDest) {
        if (cardArray.length > 0) {
            for (let i = 0; !flagAdd && i < cardArray.length; i++) {
                if (cardArray[i] != null && cardArray[i].id == idDest) {
                    cardArray.splice(i, 0, card);
                    flagAdd = true;
                } else {
                    if (cardArray[i] != null && cardArray[i].children != null && cardArray[i].children != undefined && !flagAdd) {
                        addElement(cardArray[i].children, card, idDest);
                    }
                }
            }
        }
    }

    const moveCard = (idSource: string, idDest: string) => {
        if (idSource != idDest) {
            const { card } = findCard(idSource)
            if (card != null) {
                let tempArray = sCKEditor.tagDatas;
                flagRemove = false;
                removeElement(tempArray, idSource);
                flagAdd = false;
                addElement(tempArray, card, idDest);
                sCKEditor.set_tagDatas(tempArray);
            }
        }

    }


    let result = null;

    function findCardById(cardArray, id) {
        if (cardArray.length > 0) {
            for (let i = 0; result == null && i < cardArray.length; i++) {
                if (cardArray[i] != null && cardArray[i].id == id) {
                    result = {
                        card: cardArray[i],
                        index: i,
                        level: cardArray[i].level,
                    }
                } else {
                    if (cardArray[i] != null && cardArray[i].children != null && result == null) {
                        findCardById(cardArray[i].children, id);
                    }
                }
            }
        }
        return result;
    }

    const findCard = (id: string) => {
        let tempArray = sCKEditor.tagDatas;
        result = null;
        result = findCardById(tempArray, id);
        if (result != null && result != undefined) {
            return {
                card: result.card,
                index: result.index,
                level: result.level,
            };
        } else {
            return {
                card: null,
                index: -1,
                level: -1,
            };
        }

    }

    return (
        <>
            <TreeView
                defaultCollapseIcon={<ExpandMoreIcon />}
                defaultExpandIcon={<ChevronRightIcon />}
                expanded={expandedId}
            >
                {sCKEditor.tagDatas.length > 0 && (sCKEditor.tagDatas.map(item => {
                    return (<TreeViewItem sCKEditor={sCKEditor}
                        id={item.id}
                        card={item}
                        handleClickOpen={handleClickOpen}
                        handledLabelTreeViewClick={handledLabelTreeViewClick}
                        handledOnclickTreeView={handledOnclickTreeView}
                        findCard={findCard}
                        moveCard={moveCard}
                    />)
                }))}
            </TreeView>

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
                        <TabPanelAddClasses sOverview={sOverview} node={nodeData} value={valueTag} index={0}></TabPanelAddClasses>
                        <TabPanelAddAttributes sOverview={sOverview} node={nodeData} value={valueTag} index={1}></TabPanelAddAttributes>
                        <TabPanelHTMLCode sOverview={sOverview} node={nodeData} value={valueTag} index={2}></TabPanelHTMLCode>
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

interface Item {
    type: string
    id: string
    originalIndex: string
}

export const TreeViewItem: FC<{ sCKEditor, id, card: TagData, handleClickOpen, handledLabelTreeViewClick, handledOnclickTreeView, findCard, moveCard }>
    = observer(({ sCKEditor, id, card, handleClickOpen, handledLabelTreeViewClick, handledOnclickTreeView, findCard, moveCard }) => {
        const classes = useTreeItemStyles();
        const originalIndex = findCard(id).index;
        const [{ isDragging }, drag] = useDrag(
            () => ({
                item: { type: 'card', id, originalIndex },
                collect: (monitor) => ({
                    isDragging: !!monitor.isDragging(),
                }),
                end: (item, monitor) => {
                    const { id: droppedId, originalIndex } = monitor.getItem()
                    const didDrop = monitor.didDrop()
                    if (!didDrop) {
                        moveCard(droppedId, id);
                    }
                },
            }),
            [id, originalIndex, moveCard],
        )

        const [{ isOverCurrent, isOver }, drop] = useDrop(() => ({
            accept: 'card',
            canDrop: () => true,
            drop({ id: draggedId }: Item, monitor) {
                if (draggedId !== id && monitor.isOver({ shallow: true })) {
                    moveCard(draggedId, id);
                }
            },
            collect: (monitor) => ({
                isOver: monitor.isOver({ shallow: true }),
                isOverCurrent: monitor.isOver({ shallow: true }),
            }),
        }), [findCard, moveCard])

        return (
            <TreeItem
                ref={(node) => drag(drop(node))}
                classes={{
                    root: classes.root,
                    expanded: classes.expanded,
                    selected: classes.selected,
                    group: classes.group,
                    label: classes.label,
                }}
                key={card.id}
                nodeId={card.id}
                style={sCKEditor.reactId == card.id ? { color: 'red' } : {}}
                label={<div className={classes.labelRoot} ref={(node) => drag(drop(node))}>
                    {card.name}
                    <Box ml={3} />
                    <BorderColorIcon
                        onClick={() => handleClickOpen(card)}
                    />
                </div>}
                onLabelClick={(e) => handledLabelTreeViewClick(e, card.id)}
                onClick={(e) => handledOnclickTreeView(e, card)}
            >
                {card.children != null && (
                    card.children.map(tagData => {
                        return (<TreeViewItem sCKEditor={sCKEditor}
                            id={tagData.id}
                            card={tagData}
                            handleClickOpen={handleClickOpen}
                            handledLabelTreeViewClick={handledLabelTreeViewClick}
                            handledOnclickTreeView={handledOnclickTreeView}
                            findCard={findCard}
                            moveCard={moveCard}
                        />)
                    })
                )}
            </TreeItem>
        )
    });

export const TabPanelAddClasses: FC<{ sOverview, node, value, index }> = observer(({ sOverview, node, value, index }) => {
    const classes = useTreeItemStyles();
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

    return (value == index && (
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
        </Grid>)
    );
});

export const TabPanelAddAttributes: FC<{ sOverview, node, value, index }> = observer(({ sOverview, node, value, index }) => {
    const classes = useTreeItemStyles();
    useEffect(() => {
        if (node != null) {
            sOverview.updateAttrFromData(node);
        }
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

    const onChangeValue = useCallback((key, value) => {
        sOverview.attribute.set_value(value);
        sOverview.updateAttribute(key);
    }, [sOverview]);

    return (value == index && (
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
                                    onChange={(e) => onChangeValue(x.key, e.target.value)}
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
        </Grid>)
    );
});

export const TabPanelHTMLCode: FC<{ sOverview, node, value, index }> = observer(({ sOverview, node, value, index }) => {
    const classes = useTreeItemStyles();
    const [html, setHtml] = useState("");
    let codeHtml = "";
    useEffect(() => {
        if (node != null) {
            showCodeHTML(node);
            setHtml(codeHtml);
        }
    }, [node]);

    function showCodeHTML(nodeData) {
        if (nodeData.name != undefined && nodeData.name != null) {
            let attributes = "";
            if (nodeData.props != undefined && nodeData.props != null) {
                let attrTemps = Object.entries(nodeData.props);
                for (let i = 0; i < attrTemps.length; i++) {
                    if (attrTemps[i][0] != 'reactid' && attrTemps[i][0] != 'data-reactroot') {
                        if(attrTemps[i][0].toLowerCase() == 'classname') {
                            attributes += 'class ="' + attrTemps[i][1] + '" ';
                        } else if (attrTemps[i][0] == 'style') {
                            let dataStyle = "";
                            let styleDataArray = Object.entries(attrTemps[i][1]);
                            for(let i = 0; i < styleDataArray.length; i++) {
                                let modified = styleDataArray[i][0].replaceAll(/[A-Z]/g, function(match) {
                                    return "-" + match.toLowerCase();
                                });
                                dataStyle += modified + ":" + styleDataArray[i][1];
                            }
                            attributes += 'style="' + dataStyle + '" ';
                        } else {
                            attributes += attrTemps[i][0] + '="' + attrTemps[i][1] + '" ';
                        }
                    }

                }
            }
            if (attributes != "") {
                codeHtml += "<" + nodeData.name + " " + attributes.trim() + ">\n";
            } else {
                codeHtml += "<" + nodeData.name + ">\n";
            }
            codeHtml += nodeData.text;
            if (nodeData.children != undefined && nodeData.children != null && nodeData.children.length > 0) {
                for (let i = 0; i < nodeData.children.length; i++) {
                    showCodeHTML(nodeData.children[i]);
                }
            }
            codeHtml += "</" + nodeData.name + ">\n";
        }

    }

    return (value == index && (
        <Grid container spacing={3}>
            <Paper variant="outlined" className={classes.paperContentCode} >
                <Grid item xs >
                    <pre>{html}</pre>
                </Grid>
            </Paper>
        </Grid>)
    );
});

