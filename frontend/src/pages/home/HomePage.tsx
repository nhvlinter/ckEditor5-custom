import React, { FC, ReactNode, ReactElement, useEffect, useState, useCallback, FormEvent, MouseEvent } from 'react';
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
import { renderToString } from 'react-dom/server'

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

export const HomePage: FC<{}> = observer(({ }) => {
    const { sCKEditor, sModal, routerStore } = useStore();
    const classes = useStyles();
    const [action, setAction] = useState("");
    const [openDialogAction, setOpenDialogAction] = React.useState(false);
    const [openDialog, setOpenDialog] = React.useState(false);
    const [editMode, setEditMode] = React.useState(false);
    const [mouseMove, setMouseMove] = React.useState(false);
    // const { enqueueSnackbar } = useSnackbar();
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
        console.log("Action " + action);
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
                // window.location.href = "/";
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
            sCKEditor.reactIds = [];
            sCKEditor.findAllReactIdsOfNode(node);
            e.stopPropagation();
        }
    },[sCKEditor]);

    function transform(node, index) {
        if (node.name != undefined && node.name != null) {
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
            return <node.name
                {...node.attribs}
                style={styleTag}
                onClick={(e) => handledOnclick(e, node)}
            // onMouseEnter={() => console.log("Mouse Enter")}
            // onMouseLeave={() => console.log("Mouse Leave")}
            >{processNodes(node.children, transform)}</node.name>
        }
    }

    const options = {
        decodeEntities: true,
        transform
    };

    return (<BasicLayout>
        <div>
            <h2 style={{ marginBottom: 50 }}>Inline editor</h2>
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




