import { NgModule } from '@angular/core';
import {CommonModule} from "@angular/common";
import {MydocumentComponent} from "./components/drive/tables/my/mydocument.component";
import {DepartmentComponent} from "./components/drive/tables/department/departmentdocument.component";
import {ShareddocumentComponent} from "./components/drive/tables/shared/shareddocument.component";
import {FoldercontentComponent} from "./components/drive/assets/folder/foldercontent.component";
import {Routes, RouterModule} from '@angular/router';
import {LoginGuard} from "./Guards/login.guard";

const routes: Routes = [



  {
    path: 'my',component: MydocumentComponent, canActivate: [LoginGuard],

  },
  {
    path: 'my/folder/:id',component: FoldercontentComponent,canActivate: [LoginGuard],
  },


  {
    path: 'department',component: DepartmentComponent,canActivate: [LoginGuard],
  },
  {
    path: 'department/folder/:id',component: FoldercontentComponent,canActivate: [LoginGuard],
  },


  {
    path: 'shared',component: ShareddocumentComponent,canActivate: [LoginGuard],
  },
  {
    path: 'shared/folder/:id',component: FoldercontentComponent,canActivate: [LoginGuard],
  },

];


@NgModule({
  imports: [CommonModule,RouterModule.forRoot(routes)],
  exports: [CommonModule,RouterModule],
  declarations: []
})
export class AppRoutingModule { }
