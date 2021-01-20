import { observable, action, computed, reaction, runInAction } from "mobx";
import { BaseStore } from "./BaseStore";
export class ModalStore{
    @observable isShowToast:boolean = false;
    @observable content: string = "";
    @observable toastSeverity: "success" | "info" | "warning" | "error" | undefined = "info";
    
    @observable isShowDialog:boolean = false;
    dialogMessage: any = "";
    @observable dialogTitle: string = "";

    constructor(private store: BaseStore) {
    }
    @action set_isShowToast     = (v:boolean) => { this.isShowToast    = v }
    @action set_isShowDialog    = (v:boolean) => { this.isShowDialog   = v }
    @action set_content         = (v:string)  => { this.content        = v }
    @action set_toastSeverity   = (v:"success" | "info" | "warning" | "error" | undefined)  => { this.toastSeverity  = v }

    @action set_dialogTitle     = (v:string)  => { this.dialogTitle    = v }

    @action showToastError(message:string){
        this.showToast(message, "error");
    }
    @action showToastSuccess(message:string){
        this.showToast(message, "success");    
    }
    @action showToastWarning(message:string) {
        this.showToast(message, "warning");
    }
    @action showToast(message:string, severity: "success" | "info" | "warning" | "error" | undefined){
        this.set_content(message);
        this.set_toastSeverity(severity);
        this.set_isShowDialog(false);
        this.set_isShowToast(true);
    }
    @action hideToast(){
        this.set_isShowToast(false);
    }

    @action showDialogMessage(title: string, message: any) {
        this.set_dialogTitle(title);
        this.set_isShowDialog(true);
        this.dialogMessage = message;
    }
}