import {
  Component,
  ComponentFactory,
  ComponentFactoryResolver,
  ElementRef, OnInit,
  ViewChild, ViewContainerRef,
  ViewEncapsulation
} from '@angular/core';
import {FileComponent} from "../assets/file/file.component";
import {SharedService} from "../../../Services/shared.service";
import {Router} from "@angular/router";
import {LoginService} from "../../../Services/login.service";
import {EditfileComponent} from "./modalwindow/editfile.component";
import {ShareComponent} from "./modalwindow/share.component";
import {VersionsComponent} from "./modalwindow/versions.component";

@Component({
  selector: 'filedetails',
  template: `
    <div #detailWrapper class="filedetailsWrapper" (mouseleave)="$event.stopPropagation();closeDetails();">
      <div class="fileImgContainer">
        <i class="fileIcon fileImg"></i>
        <p class="filenameText">{{file.fileDetails.filename}}</p>
        <i *ngIf="router.url === '/my' || router.url.startsWith('/my/folder')" class="editIcon addKeywordIcon" (click)="openFileUpdate()"></i>
      </div>
      <hr>
      <div class="uploadDateDetail">
        {{file.fileDetails.created_at| date: 'dd/MM/yyyy'}}
      </div>
      <div class="keywordContainer">
        <div class="keyword" *ngFor="let keyword of file.fileDetails.keywords">
          {{keyword}}
        </div>
      </div>
      <div *ngIf="router.url === '/my' || router.url.startsWith('/my/folder')" class="versionContainer" i18n>
        Other Versions: {{file.fileDetails.version_count}}<i *ngIf="file.fileDetails.version_count > 0" class="versionControlIcon versionButton" (click)="openVersionControl()"></i>
      </div>
      <div *ngIf="router.url.startsWith('/shared') && file.fileDetails.ownername != null" class="ownerContainer" i18n>
        Owner: {{file.fileDetails.ownername}}
      </div>
      <div class="fileDetailButtonContainerOpened">
        <i class="arrowIcon fileDetailButtonUp" (click)="$event.stopPropagation();this.clicked = false; closeDetails()"></i>
      </div>
    </div>
    `,
  styles: [`
    filedetails
    {
      display: flex;
      position: absolute;
      background-color: white;
      overflow: hidden;
      border: 1px solid black;
      color: #233142;
    }
    .filedetailsWrapper
    {
      width: 10vw;
      min-height: 15vw;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .fileImgContainer {
      display: flex;
      width: 100%;
      height: 24%;
      align-items: center;
    }
    .fileImg{
      margin-left: 5%;
    }
    .filenameText
    {
      margin-left: 5%;
      font-size: 1.1vw;
      text-overflow: ellipsis;
      width: 57%;
      overflow: hidden;
    }
    .keywordContainer
    {
      display: flex;
      flex-wrap: wrap;
      flex-direction: row;
      justify-content: flex-start;
      width: 90%;
      min-height: 36%;
      margin-bottom: 2%;
    }
    .addKeywordIcon
    {
      width: 1.5vw;
      height: 1.5vw;
      transition: 0.1s;
      border: 2px solid #36506C;
      border-radius: 50%;
      background-size: 65%;
      margin-top: -15%;
    }
    .addKeywordIcon:hover
    {
      background-color: #36506C;
      background-image: url("./../assets/Images/Icons/EditIconWhite.svg") !important;
    }
    .keyword
    {
      background-color: #36506C;
      color: white;
      border-radius: 11px;
      width: fit-content;
      height: fit-content;
      padding: 2%;
      margin: 1%;
      border: 2px solid #36506C;
      max-width: 100%;
      overflow: hidden;
      font-size: 0.9vw;
    }
    .versionContainer
    {
      display: flex;
      width: 100%;
      justify-content: space-evenly;
      align-items: center;
      font-size: 0.9vw;
      margin: auto;
    }
    .ownerContainer
    {
      display: flex;
      width: 100%;
      justify-content: space-evenly;
      align-items: center;
      text-align: center;
      font-size: 0.9vw;
      margin: auto;
    }
    .versionButton
    {
      width: 1.5vw;
      height: 1.5vw;
      transition: 0.1s;
      border: 2px solid #36506C;
      border-radius: 50%;
      margin-right: -6%;
      background-size: 50% !important;
      background-position: 60%;
    }
    .versionButton:hover
    {
      background-color: #36506C;
      background-image: url("./../assets/Images/Icons/VersionControlIconWhite.svg") !important;
    }
    .filedetailsWrapper > hr {
      border: none;
      width: 100%;
      background-color:black;
      height: 1px;
      margin: 0;
    }
    .uploadDateDetail
    {
      color: #233142;
      margin: 4%;
      font-size: 1vw;
    }
    .fileDetailButtonContainerOpened
    {
      display: none;
    }

    @media (max-width: 1270px) {
      .filedetailsWrapper {
        width: 22vw;
        min-height: 28vw;
      }
      .addKeywordIcon {
        width: 3.5vw;
        height: 3.5vw;
        margin-top: -8%;
      }
      .filenameText
      {
        font-size: 2.4vw;
        width: 55%;
      }
      .fileImg {
        width: 3vw;
        height: 4vw;
      }
      .uploadDateDetail {
        font-size: 2vw;
      }
      .keyword {
        font-size: 2vw;
      }
      .versionContainer {
        font-size: 1.7vw;
      }
      .ownerContainer
      {
        font-size: 1.7vw;
      }
      .versionButton {
        width: 3.5vw;
        height: 3.5vw;
        margin-right: -5%;
      }
    }

    @media (max-width: 800px) {
      .filedetailsWrapper {
        width: 26vw;
        min-height: 32vw;
      }
      .fileImg {
        width: 4vw;
        height: 5vw;
      }
      .filenameText{
        font-size: 2.8vw;
      }
      .addKeywordIcon {
        width: 4vw;
        height: 4vw;
        margin-top: -6%;
      }
      .uploadDateDetail {
        font-size: 2.6vw;
      }
      .keyword {
        font-size: 2.5vw;
      }
      .versionContainer
      {
        font-size: 2.2vw;
      }
      .ownerContainer
      {
        font-size: 2.2vw;
      }
      .versionButton {
        width: 4vw;
        height: 4vw;
      }
    }

    @media (max-width: 600px) {
      filedetails
      {
        position: relative;
        border: none;
        background-color: transparent;
        height: 100%;
      }
      .filedetailsWrapper
      {
        width: 100%;
        height: 100%;
        min-height: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
      }
      .fileImgContainer,.uploadDateDetail,.filedetailsWrapper > hr,.versionContainer {
        display: none;
      }
      .keywordContainer {
        justify-content: center;
        width: 100%;
      }
      .fileDetailButtonContainerOpened {
        display: flex;
        flex-direction: column;
        width: 100%;
        height: 23%;
        align-items: center;
        justify-content: flex-end;
        margin-top: auto;
      }

      .fileDetailButtonUp {
        transform: rotate(180deg);
        margin-bottom: 0;
        width: 5%;
        cursor: pointer;
      }
      .keyword {
        font-size: 2.4vw;
        padding: 1.3%;
      }
      .ownerContainer
      {
        font-size: 3vw;
      }
    }

  `],
  encapsulation: ViewEncapsulation.None,
})

/**
 * this class will be called when a user presses with left click on a file
 * it will allow him to see the file details like upload date and assigned keywords
 */
export class FiledetailsComponent implements OnInit
{

  // the file clicked on
  file: FileComponent;
  modalWindowInitiated: boolean;
  router;
  url: string;
  hostElement;

  @ViewChild('detailWrapper',{read: ViewContainerRef}) detailWrapper;

  // to avoid overlapping of onclick events between interaction elements and general area of drive
  clicked: boolean;

  modalwindow;
  modalWindowFactory: ComponentFactory<FileComponent
    | EditfileComponent
    | VersionsComponent>;
  factoryResolver;

  constructor(private elRef:ElementRef,
              private componentFactoryResolver: ComponentFactoryResolver,
              private shared: SharedService,
              private routerR: Router,
              private login: LoginService) {
    this.hostElement = elRef.nativeElement;
    this.factoryResolver = componentFactoryResolver;
    this.router = routerR;
    this.url = ""
    this.clicked = false;
    this.modalWindowInitiated = false;
    this.file = null;
    if(this.shared.getObject("filedetails") !== "NotFound"){
      this.shared.getObject("filedetails").clicked = false;
      this.shared.getObject("filedetails").closeDetails();
    }
    this.shared.insertObject("filedetails",this);
  }

  ngOnInit() {

  }

  /**
   * opens the file update modal window
   */
  openFileUpdate(){
    this.clicked = true;
    this.modalWindowInitiated = true;
    this.modalWindowFactory = this.componentFactoryResolver.resolveComponentFactory(EditfileComponent);
    this.modalwindow = this.detailWrapper.createComponent(this.modalWindowFactory);
    this.modalwindow.instance.setFileComponent(this.file);
    this.modalwindow.instance.parentComponent= this;
  }

  /**
   * opens the version control modal window
   */
  openVersionControl(){
    this.clicked = true;
    this.modalWindowInitiated = true;
    this.modalWindowFactory = this.componentFactoryResolver.resolveComponentFactory(VersionsComponent);
    this.modalwindow = this.detailWrapper.createComponent(this.modalWindowFactory);
    this.modalwindow.instance.version_group_id = this.file.version_group_id;
    this.modalwindow.instance.file = this.file;
    this.modalwindow.instance.parentComponent= this;
  }

  closeDetails(){
    if (!this.clicked) {
      this.detailWrapper.element.nativeElement.parentElement.parentElement.removeChild(this.detailWrapper.element.nativeElement.parentElement);
      if (this.shared.getObject("filedetails") !== "NotFound") {
        this.shared.removeObject("filedetails");
      }
      this.file.closeFileDetails();
    }
  }


}
