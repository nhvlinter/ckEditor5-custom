import React, { FC, ReactNode, ReactElement, useEffect } from 'react';
import { observer } from 'mobx-react-lite';
import { useStore } from '../../stores';
import {CKEditor} from '@ckeditor/ckeditor5-react';
//import CKEditorCustom from '../../components/CKEditor/CKEditorCustom';
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

export const HomePage: FC<{}> = observer(({}) => {
    const { sCKEditor } = useStore();
    useEffect(()=>{
        sCKEditor.init();
    });
    return (<BasicLayout><div>
            <h1 style={{marginBottom:50}}>Inline editor</h1>
            <CKEditor
            editor={InlineEditor}
            config={{extraPlugins:[DIVConversionPlugin,LabelConversionPlugin,SpanConversionPlugin,
                AnchorConversionPlugin,ImageConversionPlugin,IconConversionPlugin,FormConversionPlugin,
                UlConversionPlugin,InputConversionPlugin,TextAreaConversionPlugin,ATagConversionPlugin,
                BtnConversionPlugin]}}
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
            } }
            />
            </div>
            </BasicLayout>
        );
});




