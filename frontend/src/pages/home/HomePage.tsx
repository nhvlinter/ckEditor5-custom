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
import styles from "./HomePage.module.scss";
import { BasicLayout } from '../../layouts/BasicLayout';
import { makeStyles } from '@material-ui/core/styles';
import { Dialog, DialogActions, DialogContent, DialogContentText, DialogTitle } from '@material-ui/core';

const useStyles = makeStyles((theme) => ({
    btn: {
        '& > *': {
            margin: theme.spacing(2),
        },
    },
}));

export const HomePage: FC<{}> = observer(({ }) => {
    const { sCKEditor, sModal, routerStore } = useStore();
    const classes = useStyles();
    const [action, setAction] = useState("");
    const [openDialogAction, setOpenDialogAction] = React.useState(false);
    const [openDialog, setOpenDialog] = React.useState(false);
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
        console.log("Reseted ");
        sCKEditor.get().then(result => {
            if (result != null && result != "") {
                // sModal.showToastSuccess("Reset successful");
                window.location.href = "/";
            }
        })
    }, [sCKEditor]);

    const save = useCallback(() => {
        sCKEditor.save().then(result => {
            if (!result) {
                // sModal.showToastSuccess("Save successful");

            }
        })
    }, [sCKEditor]);


    return (<BasicLayout><div>
        <h1 style={{ marginBottom: 50 }}>Inline editor</h1>
        <CKEditor
            editor={InlineEditor}
            config={{
                extraPlugins: [DIVConversionPlugin, LabelConversionPlugin, SpanConversionPlugin,
                    AnchorConversionPlugin, ImageConversionPlugin, IconConversionPlugin, FormConversionPlugin,
                    UlConversionPlugin, InputConversionPlugin, TextAreaConversionPlugin, ATagConversionPlugin,
                    BtnConversionPlugin]
            }}
            data={sCKEditor.data}
            onReady={editor => {
                // You can store the "editor" and use when it is needed.
                console.log('Editor is ready to use!', editor);
            }}
            onChange={(event, editor) => {
                const data = editor.getData();
                sCKEditor.set_dataChanges(data);
            }}
            onBlur={(event, editor) => {
                console.log('Blur.', editor);
            }}
            onFocus={(event, editor) => {
                console.log('Focus.', editor);
            }}
        />
    </div>
        <div className={classes.btn}>
            <Button variant="contained" onClick={showDialogReset}>Reset</Button>
            <Button variant="contained" color="primary" onClick={showDialogSave}>
                Save
            </Button>
        </div>
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




