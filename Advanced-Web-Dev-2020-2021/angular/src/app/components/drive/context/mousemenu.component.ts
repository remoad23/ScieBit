import {
  Component,
  ComponentFactory,
  ComponentFactoryResolver,
  ElementRef, OnInit,
  ViewChild, ViewContainerRef,
  ViewEncapsulation
} from '@angular/core';
import {FileComponent} from "../assets/file/file.component";
import {ConfirmdeleteComponent} from "./modalwindow/confirmdelete.component";
import {ShareComponent} from "./modalwindow/share.component";
import {UploadfileComponent} from "./modalwindow/uploadfile.component";
import {SharedService} from "../../../Services/shared.service";
import {Router} from "@angular/router";
import {LoginService} from "../../../Services/login.service";
import {UploadfolderComponent} from "./modalwindow/uploadfolder.component";
import {FolderComponent} from "../assets/folder/folder.component";
import {SharefolderComponent} from "./modalwindow/sharefolder.component";
import {VersionsComponent} from "./modalwindow/versions.component";
import {EditfileComponent} from "./modalwindow/editfile.component";

@Component({
  selector: 'mousemenu',
  template: `
    <ul #interactionUL class="interactionUL">
      <li class="interactionList" *ngIf="(
      file && router.url === '/my'
      || file && router.url === '/department'
      || file && router.url.includes('my/folder/')
      || file && router.url.includes('department/folder/')
      || router.url === '/my'
      || router.url === '/department'
      || router.url.includes('my/folder/')
      || router.url.includes('department/folder/')) && !router.url.includes('shared/folder/')">
        <interaction i18n-text text="Upload File" (click)="createFile()"  [iconClass]="'addFileIcon'" ></interaction>
      </li>
      <li class="interactionList" *ngIf="
      (folder && router.url === '/my'
      || folder && router.url === '/department'
      || folder && router.url.includes('my/folder/')
      || folder && router.url.includes('department/folder/')
      || router.url === '/my'
      || router.url === '/department'
      || router.url.includes('my/folder/')
      || router.url.includes('department/folder/')) && !router.url.includes('shared/folder/')" >
        <interaction (click)="createFolder()" i18n-text text="Upload Folder"   [iconClass]="'addFolderIconWhite'" ></interaction>
      </li>
      <li class="interactionList placeholder"></li>
      <li class="interactionList" *ngIf="(file && router.url === '/my' || file && router.url.includes('my/folder/')) && !router.url.includes('shared/folder/')" >
        <interaction i18n-text text="Edit File" (click)="$event.stopPropagation();editFile();" [iconClass]="'editIconWhite'" ></interaction>
      </li>
      <li class="interactionList" *ngIf="file != null || folder != null" >
        <interaction [urlPassed]="url" (click)="$event.stopPropagation()"  [iconClass]="'downloadIconWhite'" ></interaction>
      </li>
      <li class="interactionList" *ngIf="(folder && router.url === '/my' ||  folder && router.url.includes('my/folder/')) && !router.url.includes('shared/folder/')"    >
        <interaction i18n-text text="Share Folder" (click)="shareFolder()" [iconClass]="'shareIconWhite'" ></interaction>
      </li>
      <li class="interactionList" *ngIf="(file && router.url === '/my' || file && router.url.includes('my/folder/')) && !router.url.includes('shared/folder/')"  >
        <interaction i18n-text text="Share"  (click)="shareFile()" [iconClass]="'shareIconWhite'" ></interaction>
      </li>
      <li class="interactionList" *ngIf="file && !router.url.includes('shared/folder/')" >
        <interaction i18n-text text="Delete" (click)="deleteFile()"  [iconClass]="'trashIconWhite'" ></interaction>
      </li>
      <li class="interactionList" *ngIf="folder && !router.url.includes('shared/folder/')"  >
        <interaction i18n-text text="Delete Folder" (click)="deleteFolder()" [iconClass]="'trashIconWhite'" ></interaction>
      </li>
      <li class="interactionList" *ngIf="(file && router.url === '/my' || file && router.url.includes('my/folder/')) && !router.url.includes('shared/folder/')" >
        <interaction i18n-text text="Version Control" (click)="$event.stopPropagation();showVersions()" [iconClass]="'versionControlIconWhite'" ></interaction>
      </li>
    </ul>`,
  styles: [`
    mousemenu
    {
      display: flex;
      position: absolute;
      background-color: #36506C;
      border-radius: 2%;
      overflow: hidden;
      padding: 0 1px 0 1px;
    }
    .interactionList
    {
      height: 2vw;
      width: 12vw;
      transition: 0.1s background-color;
    }
    .placeholder{
      height: 0px !important;
    }
    .interactionList:hover
    {
      background-color: #1B2133;
      cursor: pointer;
    }
    .interactionUL
    {
      height:100%;
      width:100%;
      margin: 0;
      padding: 0;
    }

    @media (max-width: 1270px)
    {
      .interactionList {
        height: 3.5vw;
        width: 18vw;
      }
    }

    @media (max-width: 800px)
    {
      .interactionList {
        height: 5vw;
        width: 26vw;
      }
    }

    @media (max-width: 600px) {
      .interactionList {
        height: 8vw;
        width: 34vw;
      }
    }

    @media (max-width: 375px)
    {
      .interactionList {
        height: 8vw;
        width: 42vw;
      }
    }
  `],
  encapsulation: ViewEncapsulation.None,
})

/**
 * this class will be called when a user presses with right click on a file
 * it will allow him to select in a dropdown between different interactions
 * like download,share,delete....
 */
export class MousemenuComponent implements OnInit
{

  // the file clicked on
  file: FileComponent;
  folder: FolderComponent;
  modalWindowInitiated: boolean;
  router;
  url: string;
  hostElement;
  /**
   * has three states:
   * file (click on a file)
   * folder (click on a folder)
   * app (when you click outside somewhere)
   */
  menuType: string;

  // to avoid overlapping of onclick events between interaction elements and general area of drive
  clicked: boolean;

  @ViewChild('interactionUL',{read: ViewContainerRef}) input;

  modalwindow;
  modalWindowFactory: ComponentFactory<ConfirmdeleteComponent
    | ShareComponent
    | UploadfileComponent
    | UploadfolderComponent
    | SharefolderComponent
    | VersionsComponent
    | EditfileComponent>;
  factoryResolver;

  constructor(private elRef:ElementRef,
              private componentFactoryResolver: ComponentFactoryResolver,
              private shared: SharedService,
              private routerR: Router,
              private login: LoginService) {
    this.hostElement = elRef.nativeElement;
    this.factoryResolver = componentFactoryResolver;
    if(this.shared.getObject('input') !== "NotFound"){
      this.shared.getObject('input').clear();
    }
    this.shared.insertObject("mousemenu",this);
    this.router = routerR;
    this.url = ""
    this.clicked = false;
    this.modalWindowInitiated = false;
    this.file = null;
    this.folder = null;

  }

  ngOnInit() {


    if(this.file && this.router.url === "/shared")
    {
      this.url = `http://localhost/Advanced-Web-Dev-2020-2021/public/drive/request/shareddocument/${this.login.id}/${this.login.token}/${this.file.fileID}/${this.file.hashName}/download`;
      return;
    }
    if(this.folder && this.router.url === "/shared")
    {
      this.url = `http://localhost/Advanced-Web-Dev-2020-2021/public/drive/request/shareddocument/folder/download/${this.login.id}/${this.login.token}/${this.folder.folderID}`;
      return;
    }
    if(this.file)
    {
      this.url = `http://localhost/Advanced-Web-Dev-2020-2021/public/drive/request/mydocument/${this.login.id}/${this.login.token}/${this.file.hashName}/download`;
    }
    if(this.folder)
    {
      this.url = `http://localhost/Advanced-Web-Dev-2020-2021/public/drive/request/mydocument/${this.login.id}/${this.login.token}/${this.folder.folderID}/folder/download`;
    }

  }

  createFile()
  {
    this.clicked = true;
    this.modalWindowInitiated = true;
    this.modalWindowFactory = this.componentFactoryResolver.resolveComponentFactory(UploadfileComponent);
    this.modalwindow = this.input.createComponent(this.modalWindowFactory);
  }

  createFolder()
  {
    this.clicked = true;
    this.modalWindowInitiated = true;
    this.modalWindowFactory = this.componentFactoryResolver.resolveComponentFactory(UploadfolderComponent);
    this.modalwindow = this.input.createComponent(this.modalWindowFactory);
  }

  shareFile()
  {
    this.clicked = true;
    this.modalWindowInitiated = true;
    this.modalWindowFactory = this.componentFactoryResolver.resolveComponentFactory(ShareComponent);
    this.modalwindow = this.input.createComponent(this.modalWindowFactory);
    this.modalwindow.instance.fileID = this.file.fileID;
  }

  shareFolder()
  {
    this.clicked = true;
    this.modalWindowInitiated = true;
    this.modalWindowFactory = this.componentFactoryResolver.resolveComponentFactory(SharefolderComponent);
    this.modalwindow = this.input.createComponent(this.modalWindowFactory);
    this.modalwindow.instance.folderID = this.folder.folderID;
  }

  deleteFile()
  {
    this.clicked = true;
    this.modalWindowInitiated = true;
    this.modalWindowFactory = this.componentFactoryResolver.resolveComponentFactory(ConfirmdeleteComponent);
    this.modalwindow = this.input.createComponent(this.modalWindowFactory);
    this.modalwindow.instance.fileID = this.file.fileID;
  }

  deleteFolder()
  {
    this.clicked = true;
    this.modalWindowInitiated = true;
    this.modalWindowFactory = this.componentFactoryResolver.resolveComponentFactory(ConfirmdeleteComponent);
    this.modalwindow = this.input.createComponent(this.modalWindowFactory);
    this.modalwindow.instance.folderID = this.folder.folderID;
  }

  showVersions()
  {
    this.clicked = true;
    this.modalWindowInitiated = true;
    this.modalWindowFactory = this.componentFactoryResolver.resolveComponentFactory(VersionsComponent);
    this.modalwindow = this.input.createComponent(this.modalWindowFactory);
    this.modalwindow.instance.version_group_id = this.file.version_group_id;
  }

  editFile(){
    this.clicked = true;
    this.modalWindowInitiated = true;
    this.modalWindowFactory = this.componentFactoryResolver.resolveComponentFactory(EditfileComponent);
    this.modalwindow = this.input.createComponent(this.modalWindowFactory);
    this.modalwindow.instance.setFileComponent(this.file);
  }

  hideWhenInterActionClicked()
  {
    this.input.element.nativeElement.style.opacity = '0';
    this.hostElement.style.backgroundColor = 'transparent';
    this.hostElement.style.border = 'none';
  }

  MyDocRouteUsed()
  {
    return this.router.url === "/my" || this.router.url.includes('my/folder/');
  }



}
