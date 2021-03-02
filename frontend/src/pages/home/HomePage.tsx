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
        children: [
            {
                name: 'span',
                children: ['Bye'],
                type: 'main',
                props: {
                    style: { color: 'white', backgroundColor: 'black' },
                },
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
        children: [
            {
                name: 'span',
                children: ['Bye'],
                type: 'main',
                props: {
                    style: { color: 'white', backgroundColor: 'black' },
                },
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
        children: [
            {
                name: 'span',
                children: ['Bye'],
                type: 'main',
                props: {
                    style: { color: 'white', backgroundColor: 'black' },
                },
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
        children: [
            {
                name: 'span',
                children: ['Bye'],
                type: 'main',
                props: {
                    style: { color: 'white', backgroundColor: 'black' },
                    id: 1
                },
            },
        ],
    },
]

export interface CardProps {
    id: string
    text: string
    name: string
    props: Object
    moveCard: (id: string, to: number) => void
    findCard: (id: string) => { index: number }
}

interface Item {
    type: string
    id: string
    originalIndex: string
}

export const Card: FC<CardProps> = ({ id, text, moveCard, name, props, children, findCard }) => {
    const originalIndex = findCard(id).index
    const [{ isDragging }, drag] = useDrag(
        () => ({
            item: { type: 'card', id, originalIndex },
            collect: (monitor) => ({
                isDragging: monitor.isDragging(),
            }),
            end: (dropResult: unknown, monitor) => {
                const { id: droppedId, originalIndex } = monitor.getItem()
                const didDrop = monitor.didDrop()
                if (!didDrop) {
                    moveCard(droppedId, originalIndex)
                }
            },
        }),
        [id, originalIndex],
    )

    const [, drop] = useDrop(() => ({
        accept: 'card',
        canDrop: () => false,
        hover({ id: draggedId }: Item) {
            if (draggedId !== id) {
                const { index: overIndex } = findCard(id)
                moveCard(draggedId, overIndex)
            }
        },
    }))

    const opacity = isDragging ? 0 : 1;

    function transform(node, index) {
        if (node.name != null && node.name != undefined) {
            return <node.name
                ref={(node) => drag(drop(node))}
                {...JSON.parse(node.attribs.props)}
            >{processNodes(node.children, transform)}</node.name>
        }
    }

    const options = {
        decodeEntities: true,
        transform
    };
    let dataReturn = "<" + name + " " + "props='" + JSON.stringify(props) +"'" + ">" + text + "</" + name + ">";
    return ReactHtmlParser(dataReturn, options);

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

    const moveCard = (id: string, atIndex: number) => {
        const { card, index } = findCard(id)
        setCards(
            update(cards, {
                $splice: [
                    [index, 1],
                    [atIndex, 0, card],
                ],
            }),
        )
    }

    const findCard = (id: string) => {
        const card = cards.filter((c) => `${c.id}` === id)[0]
        return {
            card,
            index: cards.indexOf(card),
        }
    }

    const [, drop] = useDrop(() => ({ accept: 'card' }))

    return (<BasicLayout>
        <div>
            <h2 style={{ marginBottom: 50 }}>Inline editor</h2>
            <>
                <div ref={drop} >
                    {cards.map((card) => (
                        <Card
                            key={card.id}
                            id={`${card.id}`}
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








