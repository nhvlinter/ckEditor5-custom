import React, { FC } from 'react';
import {render} from 'react-dom';
const rootEle = document.getElementById('root');

export async function bootstrap() {
    let mApp: Promise<{App:FC<{}>}>;
    mApp = import("./App") as any;
    const {App} = await mApp;
    render(<App />, rootEle);
}

bootstrap();

// If you want your app to work offline and load faster, you can change
// unregister() to register() below. Note this comes with some pitfalls.
// Learn more about service workers: https://bit.ly/CRA-PWA
import * as serviceWorker from './serviceWorker';
serviceWorker.unregister();
