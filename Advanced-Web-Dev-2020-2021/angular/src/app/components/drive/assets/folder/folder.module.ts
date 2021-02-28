import { NgModule } from '@angular/core';
import {FolderComponent} from "./folder.component";
import {CommonModule} from "@angular/common";
import {RouterModule} from "@angular/router";
import {FoldercontentComponent} from "./foldercontent.component";
import {DragDropModule} from "@angular/cdk/drag-drop";
import {BrowserAnimationsModule, NoopAnimationsModule} from "@angular/platform-browser/animations";
import {FileModule} from "../file/file.module";
import {SharefolderComponent} from "../../context/modalwindow/sharefolder.component";
import {UploadfolderComponent} from "../../context/modalwindow/uploadfolder.component";


@NgModule({
  declarations: [FolderComponent,FoldercontentComponent,SharefolderComponent,UploadfolderComponent],
  exports: [FolderComponent,FoldercontentComponent],
    imports: [CommonModule, RouterModule,
      DragDropModule,
      NoopAnimationsModule,
      BrowserAnimationsModule,FileModule],
})

export class FolderModule { }
