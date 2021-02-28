import {Component, ElementRef, Input, ViewChild, ViewContainerRef, ViewEncapsulation} from '@angular/core';
import {DriveService} from "../../../../Services/drive.service";
import {Router} from "@angular/router";
import {SharedService} from "../../../../Services/shared.service";

@Component({
  selector: 'upload',
  template: `
    <div class="modalWindowWrapper" #uploadModal >
      <div class="modalWindow">
        <h1 i18n>Upload Folder</h1>
        <label i18n>Folder</label>
        <input class="fileUploader" name="folderUpload" type="file" (change)="eventTarget = $event.target;verifyFolder()" webkitdirectory mozdirectory directory multiple>
        <div class="buttonVersioningWrapper">
          <button #submitBtn class="buttonSuccess" (click)="createFileSubmit()" disabled i18n>Upload</button>
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
    .fileUploader
    {
      height: 8vw;
      width: 100%;
      border: 3px solid #36506C;
      text-align-last: center;
      margin-bottom: 20px;
    }
    button
    {
      width: 32%;
      height: 2vw;
    }
    .buttonVersioningWrapper{
      display: flex;
      flex-direction: row;
      width: 100%;
      justify-content: space-evenly;
    }
    ::-webkit-file-upload-button {
      border: none;
      background-color: transparent;
      background-image: url("./../assets/Images/Icons/CloudIcon.svg");
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
      height: 50%;
      width: 40%;
      margin-top: 2vw;
      font-size: 0;
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
    }

    @media (max-width: 800px) {
      .modalWindow {
        width: 65vw;
        left: 9%;
      }

      .fileUploader {
        height: 20vw;
      }

      button {
        width: 35%;
        height: 7vw;
      }

      ::-webkit-file-upload-button {
        margin-top: 5vw;
        margin-left: -2vw;
      }
    }
  `],
  encapsulation: ViewEncapsulation.None,
})

export class UploadfolderComponent
{

  newFolder: any;
  folderUploaded;

  eventTarget;
  @ViewChild('submitBtn') subbtn: ElementRef;
  @ViewChild('uploadModal',{read: ViewContainerRef}) uploadModal;

  constructor(private drive: DriveService,private router: Router,private shared: SharedService) {
    this.folderUploaded = {uploaded: false};
    this.newFolder = this.newFolder = {};
    if(this.shared.getObject("mousemenu") !== "NotFound")
      this.shared.getObject('mousemenu').hideWhenInterActionClicked();
  }

  /** verify if added content is a folder
   * triggered by onchange when file/filder will be added to input element
   */
  verifyFolder()
  {
    if(this.eventTarget.files.length > 0)
    {
      this.subbtn.nativeElement.disabled = false;
    }
  }

  async createFileSubmit()
  {

    let folders;
    let isInsideFolder = false;

    if(this.router.url === "/my")
      folders = this.shared.getObject("mydocument").folders;
    else if(this.router.url === "/department")
      folders = this.shared.getObject("departmentdocument").folders;
    else if(this.router.url === "/shared")
      return;

    if(this.router.url.includes('department/folder/') || this.router.url.includes('my/folder/'))
    {
      folders = this.shared.getObject('currentFolderContent').folders;
      isInsideFolder = true;
    }


    const folder = this.eventTarget.files;

    // POST new file into db
    this.drive.makeFolder(folder,this.folderUploaded,this.newFolder,isInsideFolder);

    // wait till response from db with new created file
    while(!this.folderUploaded.uploaded)
    {
      await new Promise(resolve => setTimeout(resolve, 100));
    }
    this.folderUploaded.uploaded = false;

    // push file into list to make it visible in view
    if(folders[folders.length-1 < 0 ? 0 : folders.length-1]?.length > 7)
    {
      folders.push([]);
      folders[folders.length-1 < 0 ? 0 : folders.length-1].push(this.newFolder[0]);
    }
    else
    {
      if(!folders[0]){
        folders.push([]);
      }
      // check if its pushable ( array has a value at all)
      if(!folders[folders.length-1 < 0 ? 0 : folders.length-1]?.push(this.newFolder[0]))
      {
        //if not then push new index first
        folders.push([]);
        folders[folders.length-1 < 0 ? 0 : folders.length-1].push(this.newFolder[0]);
      }
    }
    this.eventTarget = undefined;
    this.cancel();
  }

  /**
   * get out of version modal window without saving
   */
  cancel()
  {
    if(this.shared.getObject("mousemenu") !== "NotFound" && this.shared.getObject("input") !== "NotFound")
    {
      this.shared.getObject("mousemenu").modalWindowInitiated = false;
      this.shared.getObject("mousemenu").input.remove(0);
      this.shared.getObject("input").clear();
      this.shared.removeObject("input");
    }
    else{
      //make object destroy itself
      this.uploadModal
        .element
        .nativeElement
        .parentElement
        .removeChild(this.uploadModal.element.nativeElement);
    }

  }


}
