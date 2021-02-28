import {Component, ElementRef, Input, OnInit, ViewChild, ViewContainerRef, ViewEncapsulation} from '@angular/core';
import {DriveService} from "../../../../Services/drive.service";
import {Router} from "@angular/router";
import {SharedService} from "../../../../Services/shared.service";
import {LoginService} from "../../../../Services/login.service";
import {FileComponent} from "../../assets/file/file.component";

@Component({
  selector: 'confirmdelete',
  template: `
    <div #versionWindow class="modalWindowWrapper">
      <div class="modalWindow">
        <h1 i18n>Version Control</h1>
        <select (change)="verifyVersion(selections.value)" #selections class="buttonCentred">
          <option *ngFor="let fileVersion of fileData" value="{{verifyVersion(fileVersion.id)}}">{{fileVersion.created_at | date: 'medium'}}</option>
        </select>
        <div class="buttonVersioningWrapper">
          <button class="buttonSuccess" #submitBtn (click)="resetFile()" disabled i18n>Save</button>
          <button class="buttonCancel" (click)="cancel()" i18n>Cancel</button>
        </div>
      </div>
    </div>
  `,
  styles: [`
    .modalWindow
    {
      position: absolute;
      width: 25vw;
      background-color: white;
      left: 38%;
      top: 25%;
      display: flex;
      flex-direction: column;
      padding: 35px;
      border-radius: 15px;
      height: auto !important;
      color: #36506C;
    }

    .modalWindowWrapper
    {
      width: 100vw;
      position: fixed;
      height: 100vh;
      background-color: rgba(0,0,0,0.8);
      left: 0;
      top: 0;
      z-index: 1;
    }
    button
    {
      width: 32%;
      height: 2vw;
    }
    .buttonCentred
    {
      width: 100%;
      display: flex;
      flex-direction: row;
      justify-content: space-evenly;
      height: 2vw;
      font-size: 1.2em;
      margin-bottom: 20px;
    }
    .buttonVersioningWrapper{
      display: flex;
      flex-direction: row;
      width: 100%;
      justify-content: space-evenly;
    }

    @media (max-width: 1270px) {
      .modalWindow
      {
        width: 40vw;
        left: 30%;
      }
      button
      {
        height: 3vw;
      }

      .buttonCentred
      {
        height: 3vw;
      }
    }

    @media (max-width: 800px) {
      .modalWindow {
        width: 65vw;
        left: 9%;
      }

      button {
        width: 35%;
        height: 7vw;
      }

      .buttonCentred
      {
        height: 7vw;
      }
    }

  `],
})

export class VersionsComponent implements OnInit
{

  file: FileComponent;
  // the fileData will be put in here from the HTTP request to delete frontend
  fileData;
  //fileID passed from File
  version_group_id: number;
  done;
  fileUploaded;
  parentComponent: any;
  private updatedFile;
  @ViewChild('submitBtn') subbtn: ElementRef;

  @ViewChild('selections',{read: ViewContainerRef}) select;
  @ViewChild('versionWindow',{read: ViewContainerRef}) versionWindow;


  constructor(private drive: DriveService,private router: Router,private share: SharedService,private login: LoginService) {
    this.fileUploaded = {uploaded: false};
    this.updatedFile = {};
    this.fileData = [];
    this.parentComponent = null;
    this.done =  {uploaded: false};
    if(this.share.getObject('mousemenu') !== 'NotFound'){
      this.share.getObject('mousemenu').hideWhenInterActionClicked();
    }

  }

  ngOnInit()
  {
    this.drive.getVersion(this.fileData,this.version_group_id);
  }

  verifyVersion(id)
  {
      this.subbtn.nativeElement.disabled = false;
      return id;
  }


  /**
   * set folder/file to specific version
   */
  async resetFile()
  {


    this.drive.versionize(this.updatedFile,this.select.element.nativeElement.value,this.version_group_id,this.fileUploaded)

    while(!this.fileUploaded.uploaded)
    {
      await new Promise(resolve => setTimeout(resolve, 100));
    }
    this.fileUploaded.uploaded = false;

    let files;

    if(this.router.url === "/my")
      files = this.share.getObject("mydocument").files;
    else if(this.router.url === "/department")
      files = this.share.getObject("departmentdocument").files;



    for(let i = 0; i < files.length; i++){
      for(let j = 0; j < files[i].length; j++){
        if(files[i][j].version_group_id == this.updatedFile[0].version_group_id)
        {
          files[i][j] = this.updatedFile[0];
        }
      }
    }

    if(this.share.getObject("mousemenu") != 'NotFound'){
      let mousemenu =  this.share.getObject("mousemenu");
      mousemenu.file.hashName = this.updatedFile[0].file;
      mousemenu.url = `http://localhost/Advanced-Web-Dev-2020-2021/public/drive/request/mydocument/${this.login.id}/${mousemenu.file.hashName}/download`
    }
    else{
      this.file.hashName = this.updatedFile[0].file;
      this.file = this.updatedFile[0];
    }
    this.cancel();

  }

  /**
   * get out of version modal window without saving
   */
  cancel()
  {
    if(this.parentComponent != null){
      this.parentComponent.clicked = false;
      this.parentComponent.closeDetails();
    }

    if(this.share.getObject("mousemenu") != 'NotFound' && this.share.getObject("input") != 'NotFound'){
      this.share.getObject("mousemenu").modalWindowInitiated = false;
      this.share.getObject("mousemenu").input.clear();
      this.share.getObject("input").clear();
      this.share.removeObject("input");
    }
    else{
      //make object destroy itself
      this.versionWindow
        .element
        .nativeElement
        .parentElement
        .removeChild(this.versionWindow.element.nativeElement);
    }

  }

}
