import {Component, Input, ViewChild, ViewContainerRef, ViewEncapsulation} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {DriveService} from "../../../../Services/drive.service";
import {Router} from "@angular/router";
import {SharedService} from "../../../../Services/shared.service";
import {FoldercontentComponent} from "../../assets/folder/foldercontent.component";

@Component({
  selector: 'confirmdelete',
  template: `
    <div #deleteModal class="modalWindowWrapper">
      <div class="modalWindow">
        <h1 *ngIf="fileID" i18n>Delete File?</h1>
        <h1 *ngIf="folderID" i18n>Delete Folder?</h1>
        <div class="buttonCentred">
          <button class="buttonSuccess" *ngIf="fileID" (click)="deleteFileSubmit()" i18n>Delete</button>
          <button class="buttonSuccess" *ngIf="folderID"  (click)="deleteFolderSubmit()" i18n>Delete</button>
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
    label
    {

    }
    .buttonCentred
    {
      width: 100%;
      display: flex;
      flex-direction: row;
      justify-content: space-evenly;
      margin-top: 5%;
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

      button {
        width: 35%;
        height: 7vw;
      }
    }
  `],
})

export class ConfirmdeleteComponent
{

  // the fileData will be put in here from the HTTP request to delete frontend
  private fileData;
  //fileID passed from File
  fileID: number;
  folderID: number;
  folderContent: FoldercontentComponent

  done;
  @ViewChild('deleteModal',{read: ViewContainerRef}) deleteModal;


  constructor(private drive: DriveService,private router: Router,private share: SharedService) {

    this.fileData = {};
    this.fileID = null;
    this.folderID = null;
    this.done =  {uploaded: false};
    if(this.share.getObject("mousemenu") !== "NotFound")
      this.share.getObject('mousemenu')?.hideWhenInterActionClicked();
  }

  async deleteFileSubmit()
  {

    let url;
    let sectionURL;
    if(this.router.url.startsWith("/my"))
    {
      url = "mydocument";
      sectionURL = "mydocument";
    }
    else if(this.router.url.startsWith("/department"))
    {
      url = "mydocument";
      sectionURL = "departmentdocument";
    }
    else if(this.router.url.startsWith("/shared"))
    {
      url = "shareddocument";
      sectionURL = "shareddocument";
    }

    sectionURL = this.router.url.includes('/folder/') ? "currentFolderContent" : sectionURL;

    this.drive.deleteDataByHttp(url,this.fileData,this.fileID,this.done);


    let section = this.share.getObject(sectionURL);




    // wait till response from db with new created file
    while(!this.done.uploaded)
    {
      await new Promise(resolve => setTimeout(resolve, 100));
    }

    let found = false;
    // delete file data from the array where every file data is inside
    // then align the rows again evenly
    for(let x = 0; x <= section.files.length-1; x++)
    {

      for(let y = 0; y <= section.files[x].length-1;y++)
      {
        let innerFile = section.files[x][y];
        // if file has been found then delete it and filter row to get rid of empty index
        if(innerFile.id === this.fileID)
        {
          delete section.files[x][y];
          section.files[x] = section.files[x].filter(n => n !== (undefined || null) );
          found = true;
          break;
        }
        // Make sure the deleted space will be filled with columns from next rows to make rows even
        if(found)
        {
          let previousRow = section.files[x-1];
          let currentRowColumn = section.files[x][y];
          previousRow.push(Object.assign(currentRowColumn));
          delete section.files[x][y];
          section.files[x] = section.files[x].filter(n => n !== (undefined || null) );
          break;
        }
      }
    }

    this.cancel()
  }

  async deleteFolderSubmit()
  {
    let url;
    let sectionURL;

    if(this.router.url === "/my" || this.router.url.includes('my/folder/') )
    {
      url = "mydocument";
      sectionURL = "mydocument";
    }
    else if(this.router.url === "/department" || this.router.url.includes('department/folder/'))
    {
      url = "mydocument";
      sectionURL = "departmentdocument";
    }
    else if(this.router.url === "/shared" || this.router.url.includes('shared/folder/'))
    {
      url = "shareddocument";
      sectionURL = "shareddocument";
    }

    sectionURL = this.router.url.includes('/folder/') ? "currentFolderContent" : sectionURL;


    this.drive.deleteFolder(url,this.fileData,this.folderID,this.done);


    //the folder / section(like my oder document) the user is inside at the moment
    let section = this.share.getObject(sectionURL);


    // wait till response from db with new created file
    while(!this.done.uploaded)
    {
      await new Promise(resolve => setTimeout(resolve, 100));
    }

    let found = false;
    // delete file data from the array where every file data is inside
    // then align the rows again evenly
    for(let x = 0; x <= section.folders.length-1; x++)
    {

      for(let y = 0; y <= section.folders[x].length-1;y++)
      {
        let innerFolder = section.folders[x][y];
        // if file has been found then delete it and filter row to get rid of empty index
        if(innerFolder.id === this.folderID)
        {
          delete section.folders[x][y];
          section.folders[x] = section.folders[x].filter(n => n !== (undefined || null) );
          found = true;
          break;
        }
        // Make sure the deleted space will be filled with columns from next rows to make rows even
        if(found)
        {
          let previousRow = section.folders[x-1];
          let currentRowColumn = section.folders[x][y];
          previousRow.push(Object.assign(currentRowColumn));
          delete section.folders[x][y];
          section.folders[x] = section.folders[x].filter(n => n !== (undefined || null) );
          break;
        }
      }
    }

    this.cancel()
  }

  /**
   * get out of version modal window without saving
   */
  cancel()
  {
    if(this.share.getObject("mousemenu") !== "NotFound" && this.share.getObject("input") !== "NotFound")
    {
      this.share.getObject("mousemenu").input.remove(0);
      this.share.getObject("input").clear();
      this.share.removeObject("input");
    }
    else{
      //make object destroy itself
      this.deleteModal
        .element
        .nativeElement
        .parentElement
        .removeChild(this.deleteModal.element.nativeElement);
    }
  }
}

