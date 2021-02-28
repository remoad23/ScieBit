import { NgModule } from '@angular/core';
import {FileComponent} from "./file.component";
import {CommonModule} from "@angular/common";
import {MousemenuComponent} from "../../context/mousemenu.component";
import {InteractionComponent} from "../../context/interaction.component";
import {ShareComponent} from "../../context/modalwindow/share.component";
import {ConfirmdeleteComponent} from "../../context/modalwindow/confirmdelete.component";
import {UploadfileComponent} from "../../context/modalwindow/uploadfile.component";
import {VersionsComponent} from "../../context/modalwindow/versions.component";
import {FiledetailsComponent} from "../../context/filedetails.component";
import {EditfileComponent} from "../../context/modalwindow/editfile.component";


@NgModule({
  declarations: [FileComponent,MousemenuComponent,InteractionComponent,ShareComponent,ConfirmdeleteComponent,UploadfileComponent,VersionsComponent,FiledetailsComponent,EditfileComponent],
  exports: [FileComponent,MousemenuComponent,InteractionComponent,FiledetailsComponent],
  imports: [CommonModule],
})

export class FileModule { }
