import React, { Component } from 'react';
import { Provider } from 'mobx-react';

import { StoreContext, store } from './stores';

import { RouterView } from './components/router/RouterView';

import "./css/styles.scss";

import { appViewMap } from "./routes";
import { DndProvider } from 'react-dnd';
import { HTML5Backend } from 'react-dnd-html5-backend';

export class App extends Component {
  render() {
    return (
      <DndProvider backend={HTML5Backend}>
        <StoreContext.Provider value={store}>
          <Provider store={store}><>
            <RouterView viewMap={appViewMap} />
          </></Provider>
        </StoreContext.Provider>
      </DndProvider>
    );
  }
}

export default App;