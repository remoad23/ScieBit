import { BrowserModule } from '@angular/platform-browser';
import {NgModule} from '@angular/core';
import {ShareddocumentComponent} from "./components/drive/tables/shared/shareddocument.component";
import {MydocumentComponent} from "./components/drive/tables/my/mydocument.component";
import {DepartmentComponent} from "./components/drive/tables/department/departmentdocument.component";
import {APP_BASE_HREF, CommonModule} from "@angular/common";
import { AppComponent } from './app.component';
import {AppRoutingModule} from "./app.routing";
import {HttpClientModule} from "@angular/common/http";
import {FileModule} from "./components/drive/assets/file/file.module";
import {FolderModule} from "./components/drive/assets/folder/folder.module";
import {LoginGuard} from "./Guards/login.guard";
import {DragDropModule} from '@angular/cdk/drag-drop';
import { NoopAnimationsModule } from '@angular/platform-browser/animations';
import { RouterModule } from '@angular/router';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { FormsModule } from '@angular/forms';
import { PathbuttonComponent } from './components/drive/assets/pathbutton/pathbutton.component';



@NgModule({
  declarations: [AppComponent,ShareddocumentComponent,MydocumentComponent,DepartmentComponent, PathbuttonComponent],
  imports: [
    BrowserModule,
    FileModule,
    FolderModule,
    AppRoutingModule,
    HttpClientModule,
    DragDropModule,
    NoopAnimationsModule,
    BrowserAnimationsModule,
    FormsModule,
  ],
  bootstrap: [AppComponent],
  providers: [
    { provide: APP_BASE_HREF, useValue: '/document'},
  ],
  exports: [CommonModule],
})

export class AppModule
{

}
