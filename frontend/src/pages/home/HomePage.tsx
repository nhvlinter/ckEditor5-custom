import React, { FC, ReactNode, ReactElement, useEffect } from 'react';
import { observer } from 'mobx-react-lite';
import { useStore } from '../../stores';
import {CKEditor} from '@ckeditor/ckeditor5-react';
//import CKEditorCustom from '../../components/CKEditor/CKEditorCustom';
import InlineEditor from '@ckeditor/ckeditor5-build-inline';
import DIVConversionPlugin from '../../components/CKEditor/DIVConversionPlugin';
import styles from "./HomePage.module.scss";

export const HomePage: FC<{}> = observer(({}) => {
    const { sCKEditor } = useStore();
    useEffect(()=>{
        sCKEditor.init();
    });
    return (<div>
        <h1 style={{marginBottom:50}}>Inline editor</h1>
        <CKEditor
        editor={InlineEditor}
        config={{extraPlugins:[DIVConversionPlugin]}}
        data={sCKEditor.data}
        onReady={ editor => {
            // You can store the "editor" and use when it is needed.
            console.log( 'Editor is ready to use!', editor );
        } }
        onChange={ ( event, editor ) => {
            const data = editor.getData();
            sCKEditor.update(data);
            console.log( { event, editor, data } );
        } }
        onBlur={ ( event, editor ) => {
            console.log( 'Blur.', editor );
        } }
        onFocus={ ( event, editor ) => {
            console.log( 'Focus.', editor );
        } }/></div>);
});



