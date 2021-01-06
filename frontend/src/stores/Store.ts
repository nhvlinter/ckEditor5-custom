import { observable, action, computed, reaction, runInAction } from "mobx";

import { uniqBy } from "lodash-es";
import { routes, notFound, homeRoute } from "../routes";
import { RouterStore, HistoryAdapter,  } from "mobx-state-router";
import {history} from "../services/history";
import {BaseStore} from "./BaseStore";
import { CKEditorStore } from './CKEditorStore';
export class Store extends BaseStore {
    routerStore         : RouterStore;
    constructor() {
        super();

        this.routerStore = new RouterStore(this, routes, notFound);
        const historyAdapter = new HistoryAdapter(this.routerStore, history);
        historyAdapter.observeRouterStateChanges();
    }
    sCKEditor = new CKEditorStore(this);
}
