import React, { Component } from 'react';
import { Provider } from 'mobx-react';

import { StoreContext, store } from './stores';

import { RouterView } from './components/router/RouterView';

import "./css/styles.scss";

import {appViewMap} from "./routes";

export class App extends Component {
  render() {
    return (
      <StoreContext.Provider value={store}>
        <Provider store={store}><>
          <RouterView viewMap={appViewMap} />
        </></Provider>
      </StoreContext.Provider>
    );
  }
}

export default App;