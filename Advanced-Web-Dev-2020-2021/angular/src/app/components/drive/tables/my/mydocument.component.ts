import {Component, ViewEncapsulation} from '@angular/core';
import {DriveService} from "../../../../Services/drive.service";
import {SharedService} from "../../../../Services/shared.service";
import {Router} from "@angular/router";
import {FileComponent} from "../../assets/file/file.component";
import {FolderComponent} from "../../assets/folder/folder.component";
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'mydocument',
  template: `
    <router-outlet></router-outlet>
    <div class="droplistGroupContainer" id="folderContainer">
      <div class="droplistFolder" *ngFor="let folder of folders; let k = index;">
        <folder draggable="true" (dragend)="dragEnd()" (dragstart)="dragStart($event,folder_detail)" (dragenter)="dragEnter($event, folder_detail)"
                (dragleave)="dragLeave($event)" (dragover)="allowDrop($event)" (dblclick)="goToNextFolder(folder_detail.id)"
                *ngFor="let folder_detail of folder; index as i" [hashName]="folder_detail.folder" [folderID]="folder_detail.id">
          {{folder_detail.foldername}}
        </folder>
      </div>
    </div>
    <div class="droplistGroupContainer">
      <div class="droplistMydocument" *ngFor="let file of files; let k = index;">
          <file draggable="true" (dragend)="dragEnd()" (dragstart)="dragStart($event,file_details)" *ngFor="let file_details of file; index as i"
                [fileID]="file_details.id" [hashName]="file_details.file" [version_group_id]="file_details.version_group_id" [fileDetails]="file_details">
            {{file_details.filename}}
        </file>
      </div>
    </div>
  `,
  styles:[`
    :host
    {
      width: 100%;
      height: 56vh;
      display: flex;
      padding: 4%;
      flex-direction: row;
      flex-wrap: wrap;
    }
    mydocument{
      width: 100%;
    }
    .droplistGroupContainer
    {
      width: 100%;
      height: auto;
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      margin-bottom: 2%;
    }
    .droplistMydocument
    {
      height: 80%;
      width: 100%;
      display: flex;
      flex-direction: row;
      margin-bottom: 1.5%;
    }
    .droplistFolder
    {
      height: 80%;
      width: 100%;
      display: flex;
      flex-direction: row;
      margin-bottom: 1.5%;
    }
    .draggedOver
    {
      transition: 0.2s all;
      transform: scale(1.2);
    }

    @media (max-width: 1270px) {
      .droplistMydocument {
        flex-wrap: wrap;
        margin-bottom: 0;
      }
      .droplistFolder {
        flex-wrap: wrap;
        margin-bottom: 0;
      }
    }

    @media (max-width: 600px) {

      .droplistMydocument {
        flex-wrap: nowrap;
        margin-bottom: 2%;
        flex-direction: column;
      }
      .draggedOver
      {
        transform: scale(1.1);
      }
    }
  `],
  encapsulation: ViewEncapsulation.None,
})

export class MydocumentComponent  {

  files: any;
  folders: any;
  loadedFiles: any;
  userSearchFiles: any;
  newFile: any;
  fileUploaded;
  dragElement: any;
  dropFolder: any;
  draggedOverFolderElement: any;

  drive: DriveService;

  currentSearchText = '';


   constructor(private _drive: DriveService,private shared: SharedService,private router: Router)
   {
     this.files = [];
     this.folders = [];
     this.loadedFiles = [];
     this.userSearchFiles = [];
     this.newFile = {};
     this.drive = _drive;
     this.fileUploaded = {uploaded: false};
     this.drive.getDataByHttp("mydocument",this.loadedFiles);
     this.drive.getDataByHttp("mydocumentfolder",this.folders);
     this.drive.getDataByHttp("mydocumentfiles",this.userSearchFiles);
     shared.insertObject("mydocument",this);
     this.files = this.loadedFiles;
   }



  goToNextFolder(id)
  {
    this.shared.removeObject("selectedFile");
    this.shared.removeObject("selectedFolder");
    this.shared.getObject("app").fileOrFolderSelected = false;

    if(this.router.url === "/my")
    {
      this.router.navigate([ "my/folder/"+ id]);

    }
    else{
      this.router.navigate(['my']).then(() =>{
        this.router.navigate([ "my/folder/" + id],{ replaceUrl: true });

      });
    }

  }

  /**
  * sorts the files and folder arrays by name
  */
  sortByName(){
    this.folders = this.drive.sortByNameFolders(this.folders);
    this.files = this.drive.sortByNameFiles(this.files);
  }

  /**
  * sorts the files and folder arrays by date
  */
  sortByDate(){
    this.folders = this.drive.sortByDate(this.folders);
    this.files = this.drive.sortByDate(this.files);
  }

  /**
  * filters the files array for specific filenames
  * @param searchText: text to filter
  */
  searchFiles(searchText){
    let folderContainer = document.getElementById("folderContainer");
    if (!searchText) {
      this.files = this.loadedFiles;
      folderContainer.style.display = "flex";
      return;
    }
    else{
      folderContainer.style.display = "none";
    }
    if(searchText.length < this.currentSearchText.length || searchText.length === 1){
      this.files = this.userSearchFiles;
    }
    this.currentSearchText = searchText;

    searchText = searchText.toLocaleLowerCase();

    let tempArray = [];
        for(let i = 0; i < this.files.length; i++){
          for(let j = 0; j < this.files[i].length; j++){
            tempArray.push(this.files[i][j]);
        }
    }

    tempArray = tempArray.filter(file => {
      return file.filename.toLocaleLowerCase().includes(searchText) || this.searchKeywords(file, searchText);
    });

    let tempMatrix = [];
    let matrixRows = Math.floor(tempArray.length / 8);

    for(let i = 0; i < matrixRows+1; i++){
      let tempRow = [];
      tempMatrix.push(tempRow);
    }

    for(let i = 0; i < tempMatrix.length; i++){
      for(let j = 0; j < 8; j++){
        if(tempArray[i * 8 + j] != null){
          tempMatrix[i][j] = tempArray[i * 8 + j];
        }
      }
    }
    this.files = tempMatrix;
  }

  /**
   * Searches for keywords of a file
   * @param file file which is being searched for keywords
   * @param searchText text which is compared to the keywords
   */
  searchKeywords(file, searchText){
    if(file.keywords == null){
      return false;
    }
    for(let i = 0; i < file.keywords.length; i++){
      if(file.keywords[i].toLocaleLowerCase().includes(searchText)){
        return true;
      }
    }
    return false;
  }


  /**
   * File which is dropped into input element will be uploaded asynchronously
   * into DB and view
   * @param event
   */

  async fileDrop(eventFile)
  {

    let keywords = {};
    const file = eventFile.files[eventFile.files.length-1];
    let isInsideFolder = false;
    if(this.router.url.includes('department/folder/') || this.router.url.includes('my/folder/'))
    {
      isInsideFolder = true;
    }

    // POST new file into db
    this.drive.makeFile(file,this.fileUploaded,this.newFile,keywords,isInsideFolder);

    // wait till response from db with new created file
    while(!this.fileUploaded.uploaded)
    {
      await new Promise(resolve => setTimeout(resolve, 100));
    }
    this.fileUploaded.uploaded = false;

    // push file into list to make it visible in view
    if(this.files[this.files.length-1].length > 6)
    {
      this.files.push([]);
      this.files[this.files.length-1].push(this.newFile[0]);
    }
    else
    {
      this.files[this.files.length-1].push(this.newFile[0]);
    }
    eventFile = undefined;
  }

  /**
   * Stores the dragElement inside of the dropFolder
   * @param forward: determines if the file was moved to a child folder
   */
  async putInsideFolder(forward){
    if(this.dragElement != null && this.dropFolder != null){
      if(this.dragElement?.filename)
      {
        this.drive.moveTo(this.fileUploaded,this.dropFolder.id,this.dragElement.id);

        while(!this.fileUploaded.uploaded)
        {
          await new Promise(resolve => setTimeout(resolve, 100));
        }

        this.files = this.drive.deleteOneFromFiles(this.files,this.dragElement.id);
        if(!forward){
          this.userSearchFiles = this.drive.deleteOneFromFiles(this.userSearchFiles,this.dragElement.id);
        }




      }
      else if(this.dragElement?.foldername)
      {
        this.drive.moveTo(this.fileUploaded,this.dropFolder.id,0,this.dragElement.id);

        while(!this.fileUploaded.uploaded)
        {
          await new Promise(resolve => setTimeout(resolve, 100));
        }

        this.folders = this.drive.deleteOneFromFiles(this.folders,this.dragElement.id);

      }

      this.fileUploaded.uploaded = false;

      this.dragElement = null;
      this.dropFolder = null;
    }
  }

  /**
   * Event when a file or folder is starting to get dragged
   * @param event dragEvent
   * @param dragElement the dragged file or folder
   */
  dragStart(event: DragEvent, dragElement){
    this.dragElement = dragElement;
  }

  /**
   * Event when a file or folder is no longer being dragged
   * When activated over a folder putInsideFolder()-method is called
   */
  dragEnd(){
    if(this.draggedOverFolderElement != null && this.dropFolder != null){
      if(this.draggedOverFolderElement.classList.contains("draggedOver")){
        this.draggedOverFolderElement.classList.remove("draggedOver");
        this.putInsideFolder(true);
      }
      else{
        this.draggedOverFolderElement.classList.remove("draggedOverPath");
        this.putInsideFolder(false);
      }
      this.draggedOverFolderElement = null;
    }

  }

  /**
   * Event when a folder is entered while an element is getting dragged
   * @param event dragEvent
   * @param folder entered folder
   */
  dragEnter(event: DragEvent, folder){
    if((<Element>event.target).classList != null){
      if((<Element>event.target).classList.contains("fileContainer")){
        if(this.dragElement != folder){
          this.dropFolder = folder;
          this.draggedOverFolderElement = (<Element>event.target);
          this.draggedOverFolderElement.classList.add("draggedOver");
        }
      }
    }
  }

  /**
   * Event when a folder is no longer hovered while an element is getting dragged
   * @param event dragEvent
   */
  dragLeave(event: DragEvent){
    event.preventDefault();
    if(this.draggedOverFolderElement != null){
      this.draggedOverFolderElement.classList.remove("draggedOver");
      this.draggedOverFolderElement = null;
      this.dropFolder = null;
    }
  }

  /**
   * Event when a folder is hovered while an element is getting dragged
   * @param event dragEvent
   */
  allowDrop(event: DragEvent){
    event.preventDefault();
  }
}
