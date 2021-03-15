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


export interface CardProps {
    id: string
    text: string
    name: string
    props: Object
    moveCard: (id: string, idDest: string) => void
    findCard: (id: string) => { index: number }
    children: []
    handleOnMouseEnter: (e, id) => void
    handleOnMouseLeave: (e, id) => void
    reactIdMove: string
    handledOnclick: (e, card) => void
    item: any
}

interface Item {
    type: string
    id: string
    originalIndex: string
}

export const Card: FC<CardProps> = ({ id, text, moveCard, name, props, children,
    findCard, handleOnMouseEnter, handleOnMouseLeave, reactIdMove, handledOnclick, item }) => {

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
        [id, originalIndex],
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
    }), [moveCard])

    let attributes = {};

    let styleTag = {};
    if (props.style != undefined && props.style != null) {
        const { style, ...attriTemp } = props;
        attributes = attriTemp;
        let attrTemps = Object.entries(props.style);
        for (let i = 0; i < attrTemps.length; i++) {
            styleTag[attrTemps[i][0]] = attrTemps[i][1];
        }
    } else {
        attributes = props;
    }
    if (id == reactIdMove) {
        styleTag['outline'] = "2px solid blue";
    }
    if (isOverCurrent || (isOver && id)) {
        styleTag['backgroundColor'] = "darkgreen";
    }
    const CustomTag = `${name}`;
    return (
        <CustomTag ref={(node) => drag(drop(node))}
            {...attributes}
            style={styleTag}
            onMouseEnter={(e) => handleOnMouseEnter(e, id)}
            onMouseLeave={(e) => handleOnMouseLeave(e, id)}
            onClick={(e) => handledOnclick(e, item)}
        >
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
                    handleOnMouseEnter={handleOnMouseEnter}
                    handleOnMouseLeave={handleOnMouseLeave}
                    reactIdMove={reactIdMove}
                    handledOnclick={handledOnclick}
                    item={item}
                />)}

        </CustomTag>
    )

}

export const HomePage: FC<{}> = observer(({ }) => {
    const { sCKEditor, sModal, routerStore } = useStore();
    const classes = useStyles();
    const [action, setAction] = useState("");
    const [openDialogAction, setOpenDialogAction] = React.useState(false);
    const [openDialog, setOpenDialog] = React.useState(false);
    const [editMode, setEditMode] = React.useState(false);
    const [dragDropMode, setDragDropMode] = React.useState(true);
    const [reactIdMove, setReactIdMove] = React.useState(0);
    const [cards, setCards] = useState(null);
    const listNode = [];
    const ref = useRef(null);
    useEffect(() => {
        sCKEditor.init();
        setCards(sCKEditor.tagDatas);
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

    const handledOnclick = useCallback((e, item) => {
        if (item != null) {
            sCKEditor.set_reactId(item.id);
            sCKEditor.findAllReactIdsOfNode(item);
            e.stopPropagation();
        }
    }, [sCKEditor]);

    const handleOnMouseEnter = useCallback((e, id) => {
        if (id != null) {
            setReactIdMove(id);
            e.stopPropagation();
        }
    }, [reactIdMove]);

    const handleOnMouseLeave = useCallback((e, id) => {
        if (id != null) {
            setReactIdMove(0);
            e.stopPropagation();
        }
    }, [reactIdMove]);


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

    return (<BasicLayout>
        <div>
            <h2 style={{ marginBottom: 50 }}>Inline editor</h2>
            <div className={classes.btn}>
                <Button variant="contained" onClick={showDialogReset}>Reset</Button>
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
            : <>
                {sCKEditor.tagDatas.map((card) => (card != null &&
                    <Card
                        id={card.id}
                        text={card.text}
                        moveCard={moveCard}
                        name={card.name}
                        props={card.props}
                        children={card.children}
                        findCard={findCard}
                        handleOnMouseEnter={handleOnMouseEnter}
                        handleOnMouseLeave={handleOnMouseLeave}
                        reactIdMove={reactIdMove}
                        handledOnclick={handledOnclick}
                        item={card}
                    />
                ))}
            </>}
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








