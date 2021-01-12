import { observable, action, runInAction } from "mobx";

import { LeftNavStore } from "./LeftNavStore";

export class BaseStore {
    constructor() {
    }

    sLeftNav          = new LeftNavStore(this);
}
