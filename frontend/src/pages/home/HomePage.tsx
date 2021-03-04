import React, { FC, ReactNode, ReactElement, useEffect, useState, useCallback, FormEvent, MouseEvent, useRef, useContext } from 'react';
import { observer } from 'mobx-react-lite';
import { useSnackbar } from 'notistack';
import { useStore } from '../../stores';
import { CKEditor } from '@ckeditor/ckeditor5-react';
//import CKEditorCustom from '../../components/CKEditor/CKEditorCustom';
import Button from '@material-ui/core/Button';
import InlineEditor from '@ckeditor/ckeditor5-build-inline';
import DIVConversionPlugin from '../../components/CKEditor/DIVConversionPlugin';
import LabelConversionPlugin from '../../components/CKEditor/LabelConversionPlugin';
import SpanConversionPlugin from '../../components/CKEditor/SpanConversionPlugin';
import AnchorConversionPlugin from '../../components/CKEditor/AnchorConversionPlugin';
import ImageConversionPlugin from '../../components/CKEditor/ImageConversionPlugin';
import IconConversionPlugin from '../../components/CKEditor/IconConversionPlugin';
import FormConversionPlugin from '../../components/CKEditor/FormConversionPlugin';
import UlConversionPlugin from '../../components/CKEditor/UlConversionPlugin';
import InputConversionPlugin from '../../components/CKEditor/InputConversionPlugin';
import TextAreaConversionPlugin from '../../components/CKEditor/TextAreaConversionPlugin';
import ATagConversionPlugin from '../../components/CKEditor/ATagConversionPlugin';
import BtnConversionPlugin from '../../components/CKEditor/BtnConversionPlugin';
import SectionConversionPlugin from '../../components/CKEditor/SectionConversionPlugin';
import PConverstionPlugin from '../../components/CKEditor/PConverstionPlugin';
import H3ConversionPlugin from '../../components/CKEditor/H3ConversionPlugin';
import H1ConversionPlugin from '../../components/CKEditor/H1ConversionPlugin';
import H2ConversionPlugin from '../../components/CKEditor/H2ConversionPlugin';
import H4ConversionPlugin from '../../components/CKEditor/H4ConversionPlugin';
import H5ConversionPlugin from '../../components/CKEditor/H5ConversionPlugin';
import H_P_attrConversionPlugin from '../../components/CKEditor/H_P_attrConversionPlugin';
import styles from "./HomePage.module.scss";
import { BasicLayout } from '../../layouts/BasicLayout';
import { makeStyles } from '@material-ui/core/styles';
import { Dialog, DialogActions, DialogContent, DialogContentText, DialogTitle, FormControlLabel, Switch, Box } from '@material-ui/core';
import ReactHtmlParser, { processNodes, convertNodeToElement } from 'react-html-parser';
import { renderToString } from 'react-dom/server';
import { DndProvider } from 'react-dnd';
import { HTML5Backend } from 'react-dnd-html5-backend';
import { useDrag, useDrop, DropTargetMonitor } from "react-dnd";
import { XYCoord } from "dnd-core";
import update from 'immutability-helper'

const useStyles = makeStyles((theme) => ({
    btn: {
        '& > *': {
            margin: theme.spacing(1),
        },
    },
    borderTag: {
        borderColor: '#228B22',
        borderStyle: 'solid'
    }

}));

const ITEMS = [
    {
        id: 1,
        text: 'Write a cool JS library',
        name: 'h1',
        props: {
            style: { backgroundColor: 'green' },
        },
        level: 1,
        children: [
            {
                name: 'span',
                text: 'Tag 1a',
                type: 'main',
                props: {
                    style: { color: 'white', backgroundColor: 'black' },
                },
                id: 5,
                level: 2,
            },
            {
                name: 'span',
                text: 'Tag 1b',
                type: 'main',
                props: {
                    style: { color: 'white', backgroundColor: 'black' },
                },
                id: 15,
                level: 2,
            },
        ],
    },
    {
        id: 2,
        text: 'Make it generic enough',
        name: 'h2',
        props: {
            style: { backgroundColor: 'blue' },
        },
        level: 1,
        children: [
            {
                name: 'span',
                text: 'Tag 2a',
                type: 'main',
                props: {
                    style: { color: 'white', backgroundColor: 'black' },
                },
                id: 6,
                level: 2,
            },
            {
                name: 'span',
                text: 'Tag 2b',
                type: 'main',
                props: {
                    style: { color: 'white', backgroundColor: 'black' },
                },
                id: 16,
                level: 2,
            },
        ],
    },
    {
        id: 3,
        text: 'Write README',
        name: 'h3',
        props: {
            style: { backgroundColor: 'red' },
        },
        level: 1,
        children: [
            {
                name: 'span',
                text: 'Tag 3a',
                type: 'main',
                props: {
                    style: { color: 'white', backgroundColor: 'black' },
                },
                id: 7,
                level: 2,
            },
            {
                name: 'span',
                text: 'Tag 3b',
                type: 'main',
                props: {
                    style: { color: 'white', backgroundColor: 'black' },
                },
                id: 17,
                level: 2,
            },
        ],
    },
    {
        id: 4,
        text: 'Create some examples',
        name: 'h4',
        props: {
            style: { backgroundColor: 'yellow' },
            class: "abcd xyz"
        },
        level: 1,
        children: [
            {
                name: 'span',
                text: 'Tag 4a',
                type: 'main',
                props: {
                    style: { color: 'white', backgroundColor: 'black' },
                    id: 1
                },
                id: 8,
                level: 2,
            },
            {
                name: 'span',
                text: 'Tag 4b',
                type: 'main',
                props: {
                    style: { color: 'white', backgroundColor: 'black' },
                    id: 1
                },
                id: 18,
                level: 2,
            },
        ],
    },
]

export interface CardProps {
    id: string
    text: string
    name: string
    props: Object
    moveCard: (id: string, idDest: string) => void
    findCard: (id: string) => { index: number }
    children: []
}

interface Item {
    type: string
    id: string
    originalIndex: string
}

export const Card: FC<CardProps> = ({ id, text, moveCard, name, props, children, findCard }) => {
    const originalIndex = findCard(id).index;
    const [hasDropped, setHasDropped] = useState(false)
    const [hasDroppedOnChild, setHasDroppedOnChild] = useState(false)
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
            collect: (monitor) => ({
                isDragging: monitor.isDragging(),
            }),
        }),
        [id, originalIndex],
    )

    const [{ isOverCurrent, isOver, canDrop }, drop] = useDrop(() => ({
        accept: 'card',
        canDrop: () => true,
        // hover({ id: draggedId }: Item, monitor) {
        //     if (draggedId !== id && monitor.isOver({shallow: true})) {
        //         moveCard(draggedId, id);
        //     }
        // },
        drop({ id: draggedId }: Item, monitor) {
            if (draggedId !== id && monitor.isOver({shallow: true})) {
                moveCard(draggedId, id);
            }
        },
        collect: (monitor) => ({
            isOver: monitor.isOver({shallow: true}),
            isOverCurrent: monitor.isOver({ shallow: true }),
            canDrop: monitor.canDrop(),
        }),
    }),[moveCard])

    // function transform(node, index) {
    //     if (node.name != null && node.name != undefined) {
    //         return <node.name
    //             ref={(node) => drag(drop(node))}
    //             {...JSON.parse(node.attribs.props)}
    //         >{processNodes(node.children, transform)}</node.name>
    //     }
    // }

    // const options = {
    //     decodeEntities: true,
    //     transform
    // };
    let dataReturn = "";
    // if (children != undefined && children != null) {
    //     dataReturn = "<" + name + " " + "props='" + JSON.stringify(props) + "'" + ">" +
    //         "<" + children[0].name + " " + "props='" + JSON.stringify(children[0].props) + "'" + ">" +
    //         children[0].text +
    //         "</" + children[0].name + ">"
    //         + "</" + name + ">";
    // }
    // return ReactHtmlParser(dataReturn, options);
    if (children != null && children.length > 0) {
        const CustomTag  = `${name}`;
        return (
            <CustomTag ref={(node) => drag(drop(node))} style={{ backgroundColor: 'red', padding: '10px', margin: '5px' }}>
                {text}
                {children.map(item => item != null &&
                    <Card
                        id={item.id}
                        text={item.text}
                        moveCard={moveCard}
                        name={item.name}
                        props={item.props}
                        children={item.children}
                        findCard={findCard}
                    />)}

            </CustomTag>
        )
    } else {
        const CustomTag  = `${name}`;
        return (<CustomTag ref={(node) => drag(drop(node))} style={{ backgroundColor: 'blue', color: 'white', padding: '10px', margin: '5px' }}>
            {text}
        </CustomTag>)
    }

}

export const HomePage: FC<{}> = observer(({ }) => {
    const { sCKEditor, sModal, routerStore } = useStore();
    const classes = useStyles();
    const [action, setAction] = useState("");
    const [openDialogAction, setOpenDialogAction] = React.useState(false);
    const [openDialog, setOpenDialog] = React.useState(false);
    const [editMode, setEditMode] = React.useState(false);
    const [mouseMove, setMouseMove] = React.useState(false);
    const [reactIdMove, setReactIdMove] = React.useState(0);
    const listNode = [];
    const ref = useRef(null);
    const [cards, setCards] = useState(ITEMS)
    useEffect(() => {
        sCKEditor.init();
    });

    const showDialogReset = useCallback(() => {
        setAction("reset");
        setOpenDialogAction(true);
    }, [action]);

    const showDialogSave = useCallback(() => {
        setAction("save");
        setOpenDialogAction(true);
    }, [action]);

    const handleCloseDialog = useCallback(() => {
        setOpenDialog(false);
    }, []);

    const handledCloseDialogAction = useCallback(() => {
        setAction("");
        setOpenDialogAction(false);
    }, []);

    const handledAction = useCallback(() => {
        if (action != "" && action == "reset") {
            reset();
        } else if (action != "" && action == "save") {
            save();
        } else {
            // enqueueSnackbar("Error. Can not update the CKEditor!", {
            //     variant: 'error'
            // });
        }
    }, [action]);

    const reset = useCallback(() => {
        sCKEditor.get().then(result => {
            if (result != null && result != "") {
                sCKEditor.init();
                setOpenDialogAction(false);
            }
        })
    }, [sCKEditor]);

    const save = useCallback(() => {
        sCKEditor.save().then(result => {
            sCKEditor.init();
            setOpenDialogAction(false);
        })
    }, [sCKEditor]);

    const edit = useCallback(() => {
        setEditMode(!editMode);
    }, [editMode])

    const handledOnclick = useCallback((e, node) => {
        if (node != null) {
            sCKEditor.set_reactId(node.attribs.reactid);
            sCKEditor.findAllReactIdsOfNode(node);
            e.stopPropagation();
        }
    }, [sCKEditor]);

    const handleOnMouseEnter = useCallback((e, node) => {
        if (node != null) {
            setReactIdMove(node.attribs.reactid);
            e.stopPropagation();
        }
    }, [reactIdMove]);

    const handleOnMouseLeave = useCallback((e, node) => {
        if (node != null) {
            setReactIdMove(0);
            e.stopPropagation();
        }
    }, [reactIdMove]);

    function transform(node, index) {
        if (node.name != undefined && node.name != null) {
            let reactIdNode = node.attribs.reactid;
            let styleTag = {};
            if (node.attribs.style != undefined) {
                let style = node.attribs.style;
                let arrayTemp = style.split(",");
                for (let i = 0; i < arrayTemp.length; i++) {
                    let temp = arrayTemp[i].split(":");
                    if (temp.length == 2) {
                        let key = temp[0].trim();
                        let value = temp[1].trim();
                        styleTag[key] = value.replaceAll("'", "");
                    }
                }
            }
            if (reactIdNode == reactIdMove || reactIdNode == sCKEditor.reactId) {
                styleTag['outline'] = "2px solid blue";
            }
            return <node.name
                {...node.attribs}
                style={styleTag}
                draggable="true"
                onClick={(e) => handledOnclick(e, node)}
                onMouseEnter={(e) => handleOnMouseEnter(e, node)}
                onMouseLeave={(e) => handleOnMouseLeave(e, node)}
            >{processNodes(node.children, transform)}</node.name>
        }
    }

    const options = {
        decodeEntities: true,
        transform
    };

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
            if(card != null) {
                let tempArray = cards;
                flagRemove = false;
                removeElement(tempArray, idSource);
                flagAdd = false;
                addElement(tempArray, card, idDest);
                setCards([...tempArray]);
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
        let tempArray = cards;
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

    const [, drop] = useDrop(() => ({ accept: 'card' }))



    return (<BasicLayout>
        <div>
            <h2 style={{ marginBottom: 50 }}>Inline editor</h2>
            <>
                <div ref={drop} >
                    {cards.map((card) => (card != null &&
                        <Card
                            id={card.id}
                            text={card.text}
                            moveCard={moveCard}
                            name={card.name}
                            props={card.props}
                            children={card.children}
                            findCard={findCard}
                        />
                    ))}
                </div>
            </>
            <div className={classes.btn}>
                <Button variant="contained" onClick={showDialogReset}>Reset</Button>
                {/* <Button variant="contained" color="primary" onClick={edit} >
                    Edit
                </Button> */}
                <Button variant="contained" color="primary" onClick={showDialogSave}>
                    Save
                </Button>
                <FormControlLabel
                    style={{ textAlign: "right" }}
                    control={
                        <Switch
                            checked={editMode}
                            onChange={edit}
                            name="edit"
                            color="primary"

                        />
                    }
                    labelPlacement="end"
                    label="EDIT MODE"
                />
            </div>
        </div>
        {editMode ?
            <CKEditor
                editor={InlineEditor}
                config={{
                    extraPlugins: [DIVConversionPlugin, SpanConversionPlugin,
                        // AnchorConversionPlugin, 
                        ImageConversionPlugin, IconConversionPlugin, FormConversionPlugin,
                        UlConversionPlugin, InputConversionPlugin, TextAreaConversionPlugin, ATagConversionPlugin,
                        BtnConversionPlugin, SectionConversionPlugin,
                        H_P_attrConversionPlugin,
                        LabelConversionPlugin,
                        H1ConversionPlugin,
                        H2ConversionPlugin,
                        H3ConversionPlugin,
                        H4ConversionPlugin,
                        H5ConversionPlugin,
                        PConverstionPlugin,
                    ],
                    heading: {
                        options: [
                            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                            { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                            { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                            { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                        ]
                    }
                }}

                data={renderToString(ReactHtmlParser(sCKEditor.data, options))}
                onReady={editor => {
                    // You can store the "editor" and use when it is needed.
                    console.log('Editor is ready to use!', editor);
                }}
                onChange={(event, editor) => {
                    const data = editor.getData();
                    sCKEditor.ckeditor.set_content(data);
                }}
                onBlur={(event, editor) => {
                    console.log('Blur.', editor);
                }}
                onFocus={(event, editor) => {
                    console.log('Focus.', editor);
                }}
            />
            : <div style={{ margin: '10px' }}>
                {ReactHtmlParser(sCKEditor.data, options)}
            </div>}
        <Dialog
            open={openDialogAction}
            onClose={handledCloseDialogAction}
            aria-labelledby="alert-dialog-title"
            aria-describedby="alert-dialog-description"
        >
            <DialogTitle id="alert-dialog-title">Update CKEditor</DialogTitle>
            <DialogContent>
                <DialogContentText id="alert-dialog-description">
                    {action == "save" ? "Do you want to save all changes?" :
                        action == "reset" ? "Do you want to reset all changes?" : ""}
                </DialogContentText>
            </DialogContent>
            <DialogActions>
                <Button onClick={handledCloseDialogAction} color="primary">
                    Cancel
                </Button>
                <Button onClick={handledAction} color="primary" autoFocus>
                    OK
                </Button>
            </DialogActions>
        </Dialog>
    </BasicLayout>
    );
});








